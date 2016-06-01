<div class="row">
    <nav>
        <ul class="pager">
            <li class="previous"><?php echo $this->tag->linkTo(array('preguntas/index', 'Go Back')); ?></li>
            <li class="next"><?php echo $this->tag->linkTo(array('preguntas/new', 'Create ')); ?></li>
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
                <th>Id Of Pregunta</th>
            <th>Pregunta</th>
            <th>Id Of Tipo</th>

                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($page->items)) { ?>
        <?php foreach ($page->items as $pregunta) { ?>
            <tr>
                <td><?php echo $pregunta->getIdPregunta(); ?></td>
            <td><?php echo $pregunta->getPregunta(); ?></td>
            <td><?php echo $pregunta->getIdTipo(); ?></td>

                <td><?php echo $this->tag->linkTo(array('preguntas/edit/' . $pregunta->getIdPregunta(), 'Edit')); ?></td>
                <td><?php echo $this->tag->linkTo(array('preguntas/delete/' . $pregunta->getIdPregunta(), 'Delete')); ?></td>
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
                <li><?php echo $this->tag->linkTo(array('preguntas/search', 'First')); ?></li>
                <li><?php echo $this->tag->linkTo(array('preguntas/search?page=' . $page->before, 'Previous')); ?></li>
                <li><?php echo $this->tag->linkTo(array('preguntas/search?page=' . $page->next, 'Next')); ?></li>
                <li><?php echo $this->tag->linkTo(array('preguntas/search?page=' . $page->last, 'Last')); ?></li>
            </ul>
        </nav>
    </div>
</div>
