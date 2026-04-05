// js/lowongan.js
$(document).ready(function() {
    let lowonganDataTable;
    
    function initDataTable() {
        if (lowonganDataTable) {
            lowonganDataTable.destroy();
        }
        lowonganDataTable = $('#lowonganTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            pageLength: 10
        });
    }
    
    initDataTable();
    
    // Tombol tambah lowongan
    $('#btnTambahLowongan').click(function() {
        $('#modalTitle').text('Tambah Lowongan');
        $('#lowonganForm')[0].reset();
        $('#lowonganId').val('');
        $('#gambarPreview').html('');
        $('#lowonganModal').fadeIn();
    });
    
    // Tombol edit
    $(document).on('click', '.editLowongan', function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'ajax_lowongan.php',
            type: 'GET',
            data: { action: 'get_detail', id: id },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var data = res.data;
                    $('#modalTitle').text('Edit Lowongan');
                    $('#lowonganId').val(data.id_lowongan);
                    $('#judul').val(data.judul_lowongan);
                    $('#kategori').val(data.kategori_lowongan);
                    $('#jenis').val(data.id_jenis);
                    $('#id_provinsi').val(data.id_provinsi).trigger('change');
                    setTimeout(function() {
                        $('#id_kota').val(data.id_kota);
                    }, 500);
                    $('#lokasi').val(data.lokasi_lowongan);
                    $('#gaji').val(data.gaji_lowongan);
                    $('#tgl_tutup').val(data.tanggal_tutup);
                    $('#status').val(data.status);
                    $('#kualifikasi').val(data.kualifikasi);
                    $('#deskripsi').val(data.deskripsi_lowongan);
                    if (data.gambar) {
                        $('#gambarPreview').html('<img src="uploads/lowongan/' + data.gambar + '" width="100">');
                    }
                    $('#lowonganModal').fadeIn();
                } else {
                    alert(res.message);
                }
            }
        });
    });
    
    // Tombol hapus
    $(document).on('click', '.deleteLowongan', function() {
        if (confirm('Yakin ingin menghapus lowongan ini?')) {
            var id = $(this).data('id');
            $.ajax({
                url: 'ajax_lowongan.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                dataType: 'json',
                success: function(res) {
                    alert(res.message);
                    if (res.success) location.reload();
                }
            });
        }
    });
    
    // Submit form
    $('#lowonganForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'save');
        
        $.ajax({
            url: 'ajax_lowongan.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                alert(res.message);
                if (res.success) location.reload();
            },
            error: function() {
                alert('Terjadi kesalahan');
            }
        });
    });
    
    // Preview gambar
    $('#gambar').change(function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                $('#gambarPreview').html('<img src="' + ev.target.result + '" width="100">');
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Close modal
    $('.modal-close, .modal-cancel').click(function() {
        $('#lowonganModal').fadeOut();
    });
    
    $(window).click(function(e) {
        if ($(e.target).hasClass('modal')) {
            $('.modal').fadeOut();
        }
    });
});