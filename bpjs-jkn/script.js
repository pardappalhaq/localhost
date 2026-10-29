const defaultParticipants = [
      "AHMAD JIJUY",
      "SITI AISYAH",
      "ANDI PRATAMA"
    ];

    const clinics = [
      {
        name: "Poli Umum",
        doctors: ["dr. Ahmad", "dr. Budi"]
      },
      {
        name: "Poli Gigi",
        doctors: ["drg. Sinta", "drg. Dewi"]
      },
      {
        name: "Poli Anak",
        doctors: ["dr. Rani", "dr. Fajar"]
      },
      {
        name: "Poli Penyakit Dalam",
        doctors: ["dr. Handoko, Sp.PD", "dr. Melati, Sp.PD"]
      }
    ];

    const facility = {
      name: "PUSKESMAS KOTA",
      address: "Jl. Kesehatan No. 123, Jakarta",
      phone: "021-12345678"
    };

    const form = document.querySelector("#registrationForm");
    const bpjsForm = document.querySelector("#bpjsRegistrationForm");
    const tabButtons = document.querySelectorAll(".tab-button");
    const pageSections = document.querySelectorAll(".page-section");
    const participantSelect = document.querySelector("#participant");
    const clinicSelect = document.querySelector("#clinic");
    const doctorSelect = document.querySelector("#doctor");
    const dateInput = document.querySelector("#date");
    const complaintInput = document.querySelector("#complaint");
    const nikInput = document.querySelector("#nik");
    const fullNameInput = document.querySelector("#fullName");
    const birthDateInput = document.querySelector("#birthDate");
    const genderSelect = document.querySelector("#gender");
    const phoneInput = document.querySelector("#phone");
    const emailInput = document.querySelector("#email");
    const careClassSelect = document.querySelector("#careClass");
    const primaryFacilitySelect = document.querySelector("#primaryFacility");
    const addressInput = document.querySelector("#address");
    const toggleFacilityButton = document.querySelector("#toggleFacility");
    const facilityDetail = document.querySelector("#facilityDetail");
    const modal = document.querySelector("#successModal");
    const resultList = document.querySelector("#resultList");
    const closeModalButton = document.querySelector("#closeModal");
    const modalTitle = document.querySelector("#modalTitle");
    const modalCopy = document.querySelector(".modal-copy");
    const backButton = document.querySelector(".back-button");
    const headerTitle = document.querySelector(".header-title");
    const headerSubtitle = document.querySelector(".header-subtitle");
    const pageMeta = {
      queuePage: {
        title: "Fasilitas Kesehatan Tingkat Pertama",
        subtitle: "Daftarkan antrean kunjungan FKTP dengan cepat dan nyaman."
      },
      bpjsPage: {
        title: "Pendaftaran Peserta BPJS Kesehatan",
        subtitle: "Isi data peserta baru untuk aktivasi layanan kesehatan digital."
      }
    };

    function setTodayAsDefaultDate() {
      const today = new Date();
      const timezoneOffset = today.getTimezoneOffset() * 60000;
      const localDate = new Date(today.getTime() - timezoneOffset);
      dateInput.value = localDate.toISOString().slice(0, 10);
      dateInput.min = dateInput.value;
      birthDateInput.max = dateInput.value;
    }

    function renderClinicOptions() {
      clinics.forEach((clinic) => {
        const option = document.createElement("option");
        option.value = clinic.name;
        option.textContent = clinic.name;
        clinicSelect.append(option);
      });
    }

    function renderParticipantOptions(participantNames) {
      const uniqueParticipants = [...new Set(participantNames.filter(Boolean))];
      participantSelect.innerHTML = "";

      uniqueParticipants.forEach((participantName, index) => {
        const option = document.createElement("option");
        option.value = participantName;
        option.textContent = participantName;
        option.selected = index === 0;
        participantSelect.append(option);
      });
    }

    async function loadParticipantsFromTxt() {
      try {
        const response = await fetch("/api/pendaftar");

        if (!response.ok) {
          throw new Error("Gagal mengambil data pendaftar.");
        }

        const registeredParticipants = await response.json();
        const registeredNames = registeredParticipants.map((participant) => participant.fullName);
        renderParticipantOptions(registeredNames.length > 0 ? registeredNames : defaultParticipants);
      } catch (error) {
        renderParticipantOptions(defaultParticipants);
      }
    }

    function renderDoctorOptions(clinicName) {
      const selectedClinic = clinics.find((clinic) => clinic.name === clinicName);
      doctorSelect.innerHTML = "";

      if (!selectedClinic) {
        doctorSelect.disabled = true;
        doctorSelect.append(new Option("Pilih poli terlebih dahulu", ""));
        return;
      }

      doctorSelect.disabled = false;
      doctorSelect.append(new Option("Pilih tenaga medis", ""));

      selectedClinic.doctors.forEach((doctor) => {
        doctorSelect.append(new Option(doctor, doctor));
      });
    }

    function setFacilityData() {
      document.querySelector("#facilityName").textContent = facility.name;
      document.querySelector("#facilityAddress").textContent = facility.address;
      document.querySelector("#facilityPhone").textContent = facility.phone;
    }

    function validateField(fieldName) {
      const field = form.querySelector(`[data-field="${fieldName}"]`);
      const control = field.querySelector("select, input, textarea");
      const isValid = control.value.trim() !== "";
      field.classList.toggle("invalid", !isValid);
      return isValid;
    }

    function validateForm() {
      const fields = ["participant", "clinic", "date", "doctor", "complaint"];
      const invalidFields = fields.filter((field) => !validateField(field));

      if (invalidFields.length > 0) {
        alert("Mohon lengkapi semua field pendaftaran terlebih dahulu.");
        const firstInvalid = form.querySelector(".field.invalid select, .field.invalid input, .field.invalid textarea");
        firstInvalid?.focus();
        return false;
      }

      return true;
    }

    function validateBpjsField(fieldName) {
      const field = bpjsForm.querySelector(`[data-bpjs-field="${fieldName}"]`);
      const control = field.querySelector("select, input, textarea");
      let isValid = control.value.trim() !== "";

      if (fieldName === "nik") {
        isValid = /^\d{16}$/.test(control.value.trim());
      }

      if (fieldName === "email") {
        isValid = control.validity.valid && control.value.trim() !== "";
      }

      field.classList.toggle("invalid", !isValid);
      return isValid;
    }

    function validateBpjsForm() {
      const fields = ["nik", "fullName", "birthDate", "gender", "phone", "email", "careClass", "primaryFacility", "address"];
      const invalidFields = fields.filter((field) => !validateBpjsField(field));

      if (invalidFields.length > 0) {
        alert("Mohon lengkapi semua data pendaftaran BPJS terlebih dahulu.");
        const firstInvalid = bpjsForm.querySelector(".field.invalid select, .field.invalid input, .field.invalid textarea");
        firstInvalid?.focus();
        return false;
      }

      return true;
    }

    function formatDate(dateValue) {
      return new Intl.DateTimeFormat("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric"
      }).format(new Date(`${dateValue}T00:00:00`));
    }

    function showResultModal(title, copy, rows) {
      modalTitle.textContent = title;
      modalCopy.textContent = copy;
      resultList.innerHTML = rows.map(([label, value]) => `
        <div class="result-item">
          <span>${label}</span>
          <span>${value}</span>
        </div>
      `).join("");

      modal.classList.add("show");
      closeModalButton.focus();
    }

    function showSuccessModal(data) {
      showResultModal("Pendaftaran Berhasil", "Data antrean Anda sudah tersimpan.", [
        ["Peserta", data.participant],
        ["Poli", data.clinic],
        ["Dokter", data.doctor],
        ["Tanggal", formatDate(data.date)],
        ["Keluhan", data.complaint]
      ]);
    }

    function showBpjsSuccessModal(data) {
      showResultModal("Pendaftaran BPJS Berhasil", "Data peserta baru berhasil disimpan.", [
        ["NIK", data.nik],
        ["Nama", data.fullName],
        ["Lahir", formatDate(data.birthDate)],
        ["Kelas", data.careClass],
        ["Faskes", data.primaryFacility]
      ]);
    }

    function refreshAfterSuccess() {
      window.setTimeout(() => {
        window.location.reload();
      }, 1300);
    }

    async function saveBpjsRegistration(data) {
      if (window.location.protocol === "file:") {
        throw new Error("Pendaftaran harus dibuka lewat http://localhost:3000 agar data bisa tersimpan ke file txt. Jalankan dulu: node server.js");
      }

      const response = await fetch("/api/pendaftar", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      });

      if (!response.ok) {
        const result = await response.json().catch(() => ({}));
        throw new Error(result.message || "Data gagal disimpan ke file txt.");
      }

      return response.json();
    }

    function switchPage(targetPage) {
      headerTitle.textContent = pageMeta[targetPage].title;
      headerSubtitle.textContent = pageMeta[targetPage].subtitle;

      tabButtons.forEach((button) => {
        button.classList.toggle("active", button.dataset.pageTarget === targetPage);
      });

      pageSections.forEach((section) => {
        section.classList.toggle("active", section.dataset.page === targetPage);
      });
    }

    tabButtons.forEach((button) => {
      button.addEventListener("click", () => {
        switchPage(button.dataset.pageTarget);
      });
    });

    backButton.addEventListener("click", () => {
      switchPage("queuePage");
    });

    clinicSelect.addEventListener("change", () => {
      renderDoctorOptions(clinicSelect.value);
      validateField("clinic");
    });

    [participantSelect, doctorSelect, dateInput, complaintInput].forEach((control) => {
      control.addEventListener("input", () => {
        const fieldName = control.closest(".field").dataset.field;
        validateField(fieldName);
      });
    });

    [nikInput, fullNameInput, birthDateInput, genderSelect, phoneInput, emailInput, careClassSelect, primaryFacilitySelect, addressInput].forEach((control) => {
      control.addEventListener("input", () => {
        const fieldName = control.closest(".field").dataset.bpjsField;
        validateBpjsField(fieldName);
      });
    });

    nikInput.addEventListener("input", () => {
      nikInput.value = nikInput.value.replace(/\D/g, "").slice(0, 16);
    });

    toggleFacilityButton.addEventListener("click", () => {
      const isOpen = facilityDetail.classList.toggle("open");
      toggleFacilityButton.setAttribute("aria-expanded", String(isOpen));
    });

    form.addEventListener("submit", (event) => {
      event.preventDefault();

      if (!validateForm()) {
        return;
      }

      showSuccessModal({
        participant: participantSelect.value,
        clinic: clinicSelect.value,
        doctor: doctorSelect.value,
        date: dateInput.value,
        complaint: complaintInput.value.trim()
      });
      refreshAfterSuccess();
    });

    bpjsForm.addEventListener("submit", async (event) => {
      event.preventDefault();

      if (!validateBpjsForm()) {
        return;
      }

      const bpjsData = {
        nik: nikInput.value.trim(),
        fullName: fullNameInput.value.trim(),
        birthDate: birthDateInput.value,
        gender: genderSelect.value,
        phone: phoneInput.value.trim(),
        email: emailInput.value.trim(),
        careClass: careClassSelect.value,
        primaryFacility: primaryFacilitySelect.value,
        address: addressInput.value.trim()
      };

      try {
        await saveBpjsRegistration(bpjsData);
        showBpjsSuccessModal(bpjsData);
        refreshAfterSuccess();
      } catch (error) {
        alert(error.message || "Server belum aktif. Jalankan node server.js lalu buka http://localhost:3000");
      }
    });

    closeModalButton.addEventListener("click", () => {
      modal.classList.remove("show");
    });

    modal.addEventListener("click", (event) => {
      if (event.target === modal) {
        modal.classList.remove("show");
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        modal.classList.remove("show");
      }
    });

    setFacilityData();
    loadParticipantsFromTxt();
    renderClinicOptions();
    setTodayAsDefaultDate();

