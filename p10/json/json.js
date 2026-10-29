let dataMahasiswa = [];
let fotoBaru = "";

function hapusData(index) {
    if(confirm("Apakah Anda yakin ingin menghapus data ini?")) {
        dataMahasiswa.splice(index, 1);
        tampilData();
    }
}

function tampilData() {
    let tampil = "";

    $.each(dataMahasiswa, function(i, mhs) {
        // Fix path issue if it contains 'json/image' and we are already in 'json' folder
        let fotoPath = mhs.foto;
        if(fotoPath.startsWith("json/")) {
            fotoPath = fotoPath.replace("json/", "");
        }

        tampil += `
            <div class="card">
                <button class="delete-btn" onclick="hapusData(${i})" title="Hapus Data">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <img src="${fotoPath}" alt="Foto ${mhs.nama}">
                <h3>${mhs.nama}</h3>
                <p>NIM: ${mhs.nim}</p>
            </div>
        `;
    });

    $("#mahasiswa").html(tampil);
}

$(document).ready(function() {

    $.getJSON("mahasiswa.json", function(data) {
        dataMahasiswa = data;
        tampilData();
    }).fail(function() {
        console.error("Gagal memuat data mahasiswa.json");
    });

    $("#foto").change(function(e) {
        let file = e.target.files[0];

        if(file) {
            $("#fileName").text(file.name).show();
            let reader = new FileReader();

            reader.onload = function(event) {
                fotoBaru = event.target.result;

                $("#preview")
                    .attr("src", fotoBaru)
                    .show();
            }

            reader.readAsDataURL(file);
        } else {
            $("#fileName").hide();
            $("#preview").hide();
            fotoBaru = "";
        }
    });

    $("#tambahData").click(function() {
        let nim  = $("#nim").val().trim();
        let nama = $("#nama").val().trim();

        if(nim == "" || nama == "" || fotoBaru == "") {
            alert("⚠️ Lengkapi semua data (NIM, Nama, dan Foto) terlebih dahulu!");
            return;
        }

        // Add to array at the beginning
        dataMahasiswa.unshift({
            nim: nim,
            nama: nama,
            foto: fotoBaru
        });

        tampilData();

        // Reset form
        $("#nim").val("");
        $("#nama").val("");
        $("#foto").val("");
        $("#preview").hide();
        $("#fileName").hide();
        fotoBaru = "";
    });

});