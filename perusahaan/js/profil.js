// js/profil.js
$(document).ready(function() {
    // Load kota berdasarkan provinsi
    $('#id_provinsi').on('change', function() {
        var provinsiId = $(this).val();
        if (provinsiId) {
            $.ajax({
                url: 'ajax_get_kota.php',
                type: 'GET',
                data: { id_provinsi: provinsiId },
                dataType: 'json',
                success: function(data) {
                    $('#id_kota').empty().append('<option value="">Pilih Kota</option>');
                    $.each(data, function(i, kota) {
                        $('#id_kota').append('<option value="' + kota.id_kota + '">' + kota.nama_kota + '</option>');
                    });
                }
            });
        } else {
            $('#id_kota').empty().append('<option value="">Pilih Kota</option>');
        }
    });
    
    // Upload logo
    $('#uploadLogoBtn').click(function() {
        $('#logoUpload').click();
    });
    
    $('#logoUpload').change(function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                $('#logoImg').attr('src', ev.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Submit form profil
    $('#profilForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'update');
        
        $.ajax({
            url: 'ajax_profil.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                alert(res.message);
                if (res.success) {
                    $('.company-name').text($('#namaPerusahaan').val());
                }
            },
            error: function() {
                alert('Terjadi kesalahan');
            }
        });
    });
});