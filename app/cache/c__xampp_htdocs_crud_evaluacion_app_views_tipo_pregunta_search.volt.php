<div class="row">
    <nav>
        <ul class="pager">
            <li class="previous"><?php echo $this->tag->linkTo(array('tipo_pregunta/index', 'Go Back')); ?></li>
            <li class="next"><?php echo $this->tag->linkTo(array('tipo_pregunta/new', 'Create ')); ?></li>
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
                <th>Id Of Tipo</th>
            <th>Nombre Of Tipo</th>

                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($page->items)) { ?>
        <?php foreach ($page->items as $tipo_pregunta) { ?>
            <tr>
                <td><?php echo $tipo_pregunta->getIdTipo(); ?></td>
            <td><?php echo $tipo_pregunta->getNombreTipo(); ?></td>

                <td><?php echo $this->tag->linkTo(array('tipo_pregunta/edit/' . $tipo_pregunta->getIdTipo(), 'Edit')); ?></td>
                <td><?php echo $this->tag->linkTo(array('tipo_pregunta/delete/' . $tipo_pregunta->getIdTipo(), 'Delete')); ?></td>
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
                <li><?php echo $this->tag->linkTo(array('tipo_pregunta/search', 'First')); ?></li>
                <li><?php echo $this->tag->linkTo(array('tipo_pregunta/search?page=' . $page->before, 'Previous')); ?></li>
                <li><?php echo $this->tag->linkTo(array('tipo_pregunta/search?page=' . $page->next, 'Next')); ?></li>
                <li><?php echo $this->tag->linkTo(array('tipo_pregunta/search?page=' . $page->last, 'Last')); ?></li>
            </ul>
        </nav>
    </div>
</div>
