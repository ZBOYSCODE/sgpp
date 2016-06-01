<div class="page-header">
    <h1>
        Search tipo_pregunta
    </h1>
    <p>
        <?php echo $this->tag->linkTo(array('tipo_pregunta/new', 'Create tipo_pregunta')); ?>
    </p>
</div>

<?php echo $this->getContent(); ?>

<?php echo $this->tag->form(array('tipo_pregunta/search', 'method' => 'post', 'autocomplete' => 'off', 'class' => 'form-horizontal')); ?>

<div class="form-group">
    <label for="fieldIdTipo" class="col-sm-2 control-label">Id Of Tipo</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('id_tipo', 'type' => 'numeric', 'class' => 'form-control', 'id' => 'fieldIdTipo')); ?>
    </div>
</div>

<div class="form-group">
    <label for="fieldNombreTipo" class="col-sm-2 control-label">Nombre Of Tipo</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('nombre_tipo', 'size' => 30, 'class' => 'form-control', 'id' => 'fieldNombreTipo')); ?>
    </div>
</div>


<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <?php echo $this->tag->submitButton(array('Search', 'class' => 'btn btn-default')); ?>
    </div>
</div>

</form>
