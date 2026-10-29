const http = require("http");
const fs = require("fs");
const path = require("path");

const PORT = 3000;
const ROOT_DIR = __dirname;
const DATA_FILE = path.join(ROOT_DIR, "data-pendaftar.txt");

const mimeTypes = {
  ".html": "text/html; charset=utf-8",
  ".css": "text/css; charset=utf-8",
  ".js": "application/javascript; charset=utf-8",
  ".txt": "text/plain; charset=utf-8",
  ".json": "application/json; charset=utf-8"
};

function ensureDataFile() {
  if (!fs.existsSync(DATA_FILE)) {
    fs.writeFileSync(DATA_FILE, "", "utf8");
  }
}

function sendJson(response, statusCode, payload) {
  response.writeHead(statusCode, {
    "Content-Type": "application/json; charset=utf-8"
  });
  response.end(JSON.stringify(payload));
}

function readRequestBody(request) {
  return new Promise((resolve, reject) => {
    let body = "";

    request.on("data", (chunk) => {
      body += chunk;

      if (body.length > 1_000_000) {
        reject(new Error("Ukuran data terlalu besar."));
        request.destroy();
      }
    });

    request.on("end", () => resolve(body));
    request.on("error", reject);
  });
}

function readRegistrations() {
  ensureDataFile();

  return fs
    .readFileSync(DATA_FILE, "utf8")
    .split(/\r?\n/)
    .filter(Boolean)
    .map((line) => JSON.parse(line));
}

function validateRegistration(data) {
  const requiredFields = [
    "nik",
    "fullName",
    "birthDate",
    "gender",
    "phone",
    "email",
    "careClass",
    "primaryFacility",
    "address"
  ];

  const missingField = requiredFields.find((field) => !String(data[field] || "").trim());

  if (missingField) {
    return `Field ${missingField} wajib diisi.`;
  }

  if (!/^\d{16}$/.test(data.nik)) {
    return "NIK wajib berisi 16 digit angka.";
  }

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) {
    return "Format email tidak valid.";
  }

  return "";
}

async function handleRegistrationsApi(request, response) {
  if (request.method === "GET") {
    try {
      sendJson(response, 200, readRegistrations());
    } catch (error) {
      sendJson(response, 500, {
        message: "Data pendaftar tidak bisa dibaca."
      });
    }

    return;
  }

  if (request.method === "POST") {
    try {
      const body = await readRequestBody(request);
      const data = JSON.parse(body || "{}");
      const validationError = validateRegistration(data);

      if (validationError) {
        sendJson(response, 400, {
          message: validationError
        });
        return;
      }

      const registration = {
        registeredAt: new Date().toISOString(),
        nik: data.nik.trim(),
        fullName: data.fullName.trim(),
        birthDate: data.birthDate,
        gender: data.gender,
        phone: data.phone.trim(),
        email: data.email.trim(),
        careClass: data.careClass,
        primaryFacility: data.primaryFacility,
        address: data.address.trim()
      };

      ensureDataFile();
      fs.appendFileSync(DATA_FILE, `${JSON.stringify(registration)}\n`, "utf8");

      sendJson(response, 201, {
        message: "Data berhasil disimpan.",
        registration
      });
    } catch (error) {
      sendJson(response, 400, {
        message: "Data yang dikirim tidak valid."
      });
    }

    return;
  }

  sendJson(response, 405, {
    message: "Method tidak didukung."
  });
}

function serveStaticFile(request, response) {
  const urlPath = decodeURIComponent(request.url.split("?")[0]);
  const requestedPath = urlPath === "/" ? "/index.html" : urlPath;
  const filePath = path.normalize(path.join(ROOT_DIR, requestedPath));

  if (!filePath.startsWith(ROOT_DIR)) {
    response.writeHead(403);
    response.end("Forbidden");
    return;
  }

  fs.readFile(filePath, (error, content) => {
    if (error) {
      response.writeHead(404, {
        "Content-Type": "text/plain; charset=utf-8"
      });
      response.end("File tidak ditemukan.");
      return;
    }

    const extension = path.extname(filePath);
    response.writeHead(200, {
      "Content-Type": mimeTypes[extension] || "application/octet-stream"
    });
    response.end(content);
  });
}

const server = http.createServer((request, response) => {
  if (request.url.startsWith("/api/pendaftar")) {
    handleRegistrationsApi(request, response);
    return;
  }

  serveStaticFile(request, response);
});

server.listen(PORT, () => {
  console.log(`Server berjalan di http://localhost:${PORT}`);
});
