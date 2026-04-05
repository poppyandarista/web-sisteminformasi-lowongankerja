// js/pelamar.js
$(document).ready(function() {
    let pelamarDataTable;
    
    function initDataTable() {
        if (pelamarDataTable) {
            pelamarDataTable.destroy();
        }
        pelamarDataTable = $('#pelamarTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            pageLength: 10
        });
    }
    
    initDataTable();
    
    // Tombol detail pelamar
    $(document).on('click', '.detailPelamar', function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'ajax_pelamar.php',
            type: 'GET',
            data: { action: 'get_detail', id: id },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var data = res.data;
                    var html = `
                        <div class="detail-item"><strong>Nama Lengkap</strong><p>${data.nama_user || data.username_user || '-'}</p></div>
                        <div class="detail-item"><strong>Email</strong><p>${data.email_user || '-'}</p></div>
                        <div class="detail-item"><strong>No Telepon</strong><p>${data.nohp_user || '-'}</p></div>
                        <div class="detail-item"><strong>Tanggal Lahir</strong><p>${data.tanggallahir_user || '-'}</p></div>
                        <div class="detail-item"><strong>Jenis Kelamin</strong><p>${data.jk_user == 'L' ? 'Laki-laki' : (data.jk_user == 'P' ? 'Perempuan' : '-')}</p></div>
                        <div class="detail-item"><strong>Alamat</strong><p>${data.nama_provinsi || ''} ${data.nama_kota || ''}</p></div>
                        <div class="detail-item"><strong>Deskripsi Diri</strong><p>${data.deskripsi_user || '-'}</p></div>
                        <div class="detail-item"><strong>Kelebihan</strong><p>${data.kelebihan_user || '-'}</p></div>
                        <div class="detail-item"><strong>Riwayat Pekerjaan</strong><p>${data.riwayatpekerjaan_user || '-'}</p></div>
                        <div class="detail-item"><strong>Prestasi</strong><p>${data.prestasi_user || '-'}</p></div>
                        <div class="detail-item"><strong>Instagram</strong><p>${data.instagram_user || '-'}</p></div>
                        <div class="detail-item"><strong>Facebook</strong><p>${data.facebook_user || '-'}</p></div>
                        <div class="detail-item"><strong>LinkedIn</strong><p>${data.linkedin_user || '-'}</p></div>
                    `;
                    if (data.foto_user) {
                        html = `<div class="detail-item"><strong>Foto</strong><p><img src="../uploads/profil/${data.foto_user}" width="100" style="border-radius: 50%;"></p></div>` + html;
                    }
                    $('#detailContent').html(html);
                    $('#detailModal').fadeIn();
                } else {
                    alert(res.message);
                }
            }
        });
    });
    
    // Close modal
    $('.modal-close').click(function() {
        $('#detailModal').fadeOut();
    });
    
    $(window).click(function(e) {
        if ($(e.target).hasClass('modal')) {
            $('.modal').fadeOut();
        }
    });
});