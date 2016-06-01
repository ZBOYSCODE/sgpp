<div class="row">
    <nav>
        <ul class="pager">
            <li class="previous"><?php echo $this->tag->linkTo(array('usuarios/index', 'Go Back')); ?></li>
            <li class="next"><?php echo $this->tag->linkTo(array('usuarios/new', 'Create ')); ?></li>
        </ul>
    </nav>
</div>

<div class="page-header">
    <h1>Search result</h1>
</div>

<?php echo $this->getContent(); ?>

<div class="row">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Rut</th>
            <th>Apellido Of Paterno</th>
            <th>Correo</th>
            <th>Apellido Of Materno</th>
            <th>Nombres</th>
            <th>Area</th>

                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($page->items)) { ?>
        <?php foreach ($page->items as $usuario) { ?>
            <tr>
                <td><?php echo $usuario->getRut(); ?></td>
            <td><?php echo $usuario->getApellidoPaterno(); ?></td>
            <td><?php echo $usuario->getCorreo(); ?></td>
            <td><?php echo $usuario->getApellidoMaterno(); ?></td>
            <td><?php echo $usuario->getNombres(); ?></td>
            <td><?php echo $usuario->getArea(); ?></td>

                <td><?php echo $this->tag->linkTo(array('usuarios/edit/' . $usuario->getRut(), 'Edit')); ?></td>
                <td><?php echo $this->tag->linkTo(array('usuarios/delete/' . $usuario->getRut(), 'Delete')); ?></td>
            </tr>
        <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-sm-1">
        <p class="pagination" style="line-height: 1.42857;padding: 6px 12px;">
            <?php echo $page->current . '/' . $page->total_pages; ?>
        </p>
    </div>
    <div class="col-sm-11">
        <nav>
            <ul class="pagination">
                <li><?php echo $this->tag->linkTo(array('usuarios/search', 'First')); ?></li>
                <li><?php echo $this->tag->linkTo(array('usuarios/search?page=' . $page->before, 'Previous')); ?></li>
                <li><?php echo $this->tag->linkTo(array('usuarios/search?page=' . $page->next, 'Next')); ?></li>
                <li><?php echo $this->tag->linkTo(array('usuarios/search?page=' . $page->last, 'Last')); ?></li>
            </ul>
        </nav>
    </div>
</div>
