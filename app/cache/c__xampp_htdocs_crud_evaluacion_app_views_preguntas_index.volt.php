<div class="page-header">
    <h1>
        Search preguntas
    </h1>
    <p>
        <?php echo $this->tag->linkTo(array('preguntas/new', 'Create preguntas')); ?>
    </p>
</div>

<?php echo $this->getContent(); ?>

<?php echo $this->tag->form(array('preguntas/search', 'method' => 'post', 'autocomplete' => 'off', 'class' => 'form-horizontal')); ?>

<div class="form-group">
    <label for="fieldIdPregunta" class="col-sm-2 control-label">Id Of Pregunta</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('id_pregunta', 'type' => 'numeric', 'class' => 'form-control', 'id' => 'fieldIdPregunta')); ?>
    </div>
</div>

<div class="form-group">
    <label for="fieldPregunta" class="col-sm-2 control-label">Pregunta</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('pregunta', 'size' => 30, 'class' => 'form-control', 'id' => 'fieldPregunta')); ?>
    </div>
</div>

<div class="form-group">
    <label for="fieldIdTipo" class="col-sm-2 control-label">Id Of Tipo</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('id_tipo', 'type' => 'numeric', 'class' => 'form-control', 'id' => 'fieldIdTipo')); ?>
    </div>
</div>


<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <?php echo $this->tag->submitButton(array('Search', 'class' => 'btn btn-default')); ?>
    </div>
</div>

</form>
