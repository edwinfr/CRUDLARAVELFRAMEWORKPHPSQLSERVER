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

<script>
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    let modo = 'crear'; // o 'editar'

    function cargarPosts() {
        $.get("posts/list", function (data) {
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

    $(function () {
        cargarPosts();

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
            if (!confirm('¿Eliminar este registro?')) return;
            const id = $(this).closest('tr').data('id');
            $.ajax({
                url: `/posts/${id}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function () {
                    cargarPosts();
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

            let url = "{{ route('posts.store') }}";
            let type = 'POST';

            if (modo === 'editar') {
                url = `/posts/${id}`;
                type = 'PUT';
            }

            $.ajax({
                url,
                type,
                data: datos,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function () {
                    bootstrap.Modal.getInstance(document.getElementById('modalPost')).hide();
                    cargarPosts();
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