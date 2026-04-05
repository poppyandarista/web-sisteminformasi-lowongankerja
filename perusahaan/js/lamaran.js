// js/lamaran.js
$(document).ready(function() {
    let lamaranDataTable;
    
    function initDataTable() {
        if (lamaranDataTable) {
            lamaranDataTable.destroy();
        }
        lamaranDataTable = $('#lamaranTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            pageLength: 10,
            order: [[2, 'desc']]
        });
    }
    
    initDataTable();
    
    // Filter status
    $('#filterStatus').change(function() {
        var filter = $(this).val();
        if (filter === 'all') {
            lamaranDataTable.columns(3).search('').draw();
        } else {
            lamaranDataTable.columns(3).search(filter).draw();
        }
    });
    
    // Tombol update status
    $(document).on('click', '.updateLamaran', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        var catatan = $(this).data('catatan');
        
        $('#lamaranId').val(id);
        $('#updateStatus').val(status);
        $('#updateCatatan').val(catatan || '');
        $('#catatanModal').fadeIn();
    });
    
    // Submit form catatan
    $('#catatanForm').submit(function(e) {
        e.preventDefault();
        var id = $('#lamaranId').val();
        var status = $('#updateStatus').val();
        var catatan = $('#updateCatatan').val();
        
        $.ajax({
            url: 'ajax_lamaran.php',
            type: 'POST',
            data: {
                action: 'update_status',
                id: id,
                status: status,
                catatan: catatan
            },
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
    
    // Close modal
    $('.modal-close, .modal-cancel').click(function() {
        $('#catatanModal').fadeOut();
    });
    
    $(window).click(function(e) {
        if ($(e.target).hasClass('modal')) {
            $('.modal').fadeOut();
        }
    });
});