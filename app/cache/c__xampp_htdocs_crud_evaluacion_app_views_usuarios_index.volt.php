<div class="page-header">
    <h1>
        Search usuarios
    </h1>
    <p>
        <?php echo $this->tag->linkTo(array('usuarios/new', 'Create usuarios')); ?>
    </p>
</div>

<?php echo $this->getContent(); ?>

<?php echo $this->tag->form(array('usuarios/search', 'method' => 'post', 'autocomplete' => 'off', 'class' => 'form-horizontal')); ?>

<div class="form-group">
    <label for="fieldRut" class="col-sm-2 control-label">Rut</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('rut', 'size' => 30, 'class' => 'form-control', 'id' => 'fieldRut')); ?>
    </div>
</div>

<div class="form-group">
    <label for="fieldApellidoPaterno" class="col-sm-2 control-label">Apellido Of Paterno</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('apellido_paterno', 'size' => 30, 'class' => 'form-control', 'id' => 'fieldApellidoPaterno')); ?>
    </div>
</div>

<div class="form-group">
    <label for="fieldCorreo" class="col-sm-2 control-label">Correo</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('correo', 'size' => 30, 'class' => 'form-control', 'id' => 'fieldCorreo')); ?>
    </div>
</div>

<div class="form-group">
    <label for="fieldApellidoMaterno" class="col-sm-2 control-label">Apellido Of Materno</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('apellido_materno', 'size' => 30, 'class' => 'form-control', 'id' => 'fieldApellidoMaterno')); ?>
    </div>
</div>

<div class="form-group">
    <label for="fieldNombres" class="col-sm-2 control-label">Nombres</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('nombres', 'size' => 30, 'class' => 'form-control', 'id' => 'fieldNombres')); ?>
    </div>
</div>

<div class="form-group">
    <label for="fieldArea" class="col-sm-2 control-label">Area</label>
    <div class="col-sm-10">
        <?php echo $this->tag->textField(array('area', 'size' => 30, 'class' => 'form-control', 'id' => 'fieldArea')); ?>
    </div>
</div>


<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <?php echo $this->tag->submitButton(array('Search', 'class' => 'btn btn-default')); ?>
    </div>
</div>

</form>
