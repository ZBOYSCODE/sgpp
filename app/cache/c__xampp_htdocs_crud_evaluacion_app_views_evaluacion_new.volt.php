<div class="row">
    <nav>
        <ul class="pager">
            <li class="previous"><?php echo $this->tag->linkTo(array('evaluacion', 'Go Back')); ?></li>
        </ul>
    </nav>
</div>

<div class="page-header">
    <h1>
        Create evaluacion
    </h1>
</div>

<?php echo $this->getContent(); ?>

<?php echo $this->tag->form(array('evaluacion/create', 'method' => 'post', 'autocomplete' => 'off', 'class' => 'form-horizontal')); ?>

<div class="form-group">
    <label for="fieldPuntaje" class="col-sm-2 control-label">Puntaje</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('puntaje', 'type' => 'numeric', 'class' => 'form-control', 'id' => 'fieldPuntaje')); ?>
    </div>
</div>

<div class="form-group">
    <label for="fieldRutEvaluador" class="col-sm-2 control-label">Rut Of Evaluador</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('rut_evaluador', 'size' => 30, 'class' => 'form-control', 'id' => 'fieldRutEvaluador')); ?>
    </div>
</div>

<div class="form-group">
    <label for="fieldRutEvaluado" class="col-sm-2 control-label">Rut Of Evaluado</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('rut_evaluado', 'size' => 30, 'class' => 'form-control', 'id' => 'fieldRutEvaluado')); ?>
    </div>
</div>

<div class="form-group">
    <label for="fieldIdPregunta" class="col-sm-2 control-label">Id Of Pregunta</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('id_pregunta', 'type' => 'numeric', 'class' => 'form-control', 'id' => 'fieldIdPregunta')); ?>
    </div>
</div>


<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <?php echo $this->tag->submitButton(array('Save', 'class' => 'btn btn-default')); ?>
    </div>
</div>

</form>
