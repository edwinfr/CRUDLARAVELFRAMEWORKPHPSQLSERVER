<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CRUD Posts</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="p-4">
<div class="container">
    <h1 class="mb-4">CRUD de Posts (Laravel + Ajax)</h1>

    <button class="btn btn-primary mb-3" id="btnNuevo">Nuevo Post</button>

    <table class="table table-bordered" id="tablaPosts">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Contenido</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        {{-- filas via Ajax --}}
        </tbody>
    </table>
</div>

{{-- Modal --}}
<div class="modal fade" id="modalPost" tabindex="-1">
    <div class="modal-dialog">
        <form id="formPost" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModal">Nuevo Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="postId">
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" class="form-control" id="title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contenido</label>
                    <textarea class="form-control" id="content" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar</button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    let modo = 'crear'; // o 'editar'
    $(function () {



let tabla = $('#tablaPosts').DataTable({

ajax:{
url:"/posts/list",
dataSrc:""
},

columns:[
{data:"id"},
{data:"title"},
{data:"content"},
{
data:null,
render:function(data){
return `
<button class="btn btn-warning btn-edit  btnEditar" data-id="${data.id}">
Editar
</button>

<button class="btn btn-danger btn-delete  btnEliminar" data-id="${data.id}" data-title="${data.title}">
Eliminar
</button>
`;
}
}
],
order:[[0,'desc']],
paging:true,
searching:true,
info:true,
lengthMenu:[5,10,25,50],

language:{
url:"https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
}

});






function loadData(){

$.ajax({

url:"/posts/list",
method:"GET",
success:function(data){

tabla.clear();

data.forEach(function(row){

tabla.row.add([

row.ID,
row.TITLE,
row.CONTENT,

`
<button class="btn btn-warning btn-edit"
data-id="${row.ID}"
data-nombre="${row.TITLE}"
data-email="${row.CONTENT}">
Editar
</button>

<button class="btn btn-danger btn-delete"
data-id="${row.ID}"
data-nombre="${row.TITLE}"
data-email="${row.CONTENT}">
Eliminar
</button>

`

]);

});

tabla.draw();

}

});

}
//loadData();

    function cargarPosts() {
        $.get("/posts/list", function (data) {
            const $tbody = $('#tablaPosts tbody');
            $tbody.empty();
            data.forEach(post => {
                $tbody.append(`
                    <tr data-id="${post.id}">
                        <td>${post.id}</td>
                        <td>${post.title}</td>
                        <td>${post.content ?? ''}</td>
                        <td>
                            <button class="btn btn-sm btn-warning btnEditar">Editar</button>
                            <button class="btn btn-sm btn-danger btnEliminar">Eliminar</button>
                        </td>
                    </tr>
                `);
            });
        });
    }


       // cargarPosts();

        $('#btnNuevo').on('click', function () {
            modo = 'crear';
            $('#tituloModal').text('Nuevo Post');
            $('#postId').val('');
            $('#title').val('');
            $('#content').val('');
            const modal = new bootstrap.Modal(document.getElementById('modalPost'));
            modal.show();
        });

        $('#tablaPosts').on('click', '.btnEditar', function () {
            modo = 'editar';
            const $tr = $(this).closest('tr');
            $('#postId').val($tr.data('id'));
            $('#title').val($tr.find('td:nth-child(2)').text());
            $('#content').val($tr.find('td:nth-child(3)').text());
            $('#tituloModal').text('Editar Post');
            const modal = new bootstrap.Modal(document.getElementById('modalPost'));
            modal.show();
        });

        $('#tablaPosts').on('click', '.btnEliminar', function () {
            if (!confirm('¿Eliminar este registro?\n'+$(this).data('id')+":"+$(this).data('title'))) return;
            const id = $(this).data('id');
            $.ajax({
                url: `/posts/${id}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function () {
                    tabla.ajax.reload();
                }
            });
        });

        $('#formPost').on('submit', function (e) {
            e.preventDefault();
            const id = $('#postId').val();
            const datos = {
                title: $('#title').val(),
                content: $('#content').val()
            };

            let url = "/posts";
            let type = 'POST';

            if (modo === 'editar') {
                url = `/posts/${id}`;
                type = 'POST';
            }

            $.ajax({
                url,
                type,
                data: datos,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function () {
                    bootstrap.Modal.getInstance(document.getElementById('modalPost')).hide();
                   tabla.ajax.reload();
                },
                error: function (xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });
    });
</script>
</body>
</html>