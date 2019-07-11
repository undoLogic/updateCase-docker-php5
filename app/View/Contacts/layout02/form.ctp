<?php $this->SetupCase->loadPageBySlug(Configure::read("SetupCase.slug")); ?>


<div class="container">

    <div class="row">
        <div class="col-lg-12 contact-banner">
            <h1><?= $this->SetupCase->getContentBy('Full', 'title'); ?></h1>












            <div class="row">

                <div class="col-lg-6 col-md-6">


                    <?php $groups = $this->SetupCase->getGroupNamesByLocation("Full"); ?>
                    <?php foreach ($groups as $group): ?>

                        <div class="col-lg-12 col-md-12">
                            <img src="<?= $this->SetupCase->getUrlBy("Full", "image", $group); ?>"/>

                            <h3><i class="fa <?= $this->SetupCase->getTextOnlyBy("Full", "icon", $group); ?> fa-2x"></i>
                                <?= $this->SetupCase->getContentBy("Full", "title", $group); ?>
                            </h3>

                            <?= $this->SetupCase->getContentBy("Full", "text", $group); ?>

                        </div>
                    <?php endforeach; ?>



                </div>

                <div class="col-lg-6 col-md-6">

                    <?= $this->Form->create('Contact'
                      //  , array(
                        //'url' => 'www.undologic.com',
                        //'action' => 'index'
                    //)
                    ); ?>

                    <?= $this->Form->input('name', array(
                        'label' => 'Name',
                        'class' => 'form-control'
                    )); ?>

                    <?= $this->Form->input('email', array(
                        'label' => 'Email Address',
                        'class' => 'form-control'
                    )); ?>

                    <?= $this->Form->input('comments', array(
                        'label' => 'Comments',
                        'type' => 'textarea',
                        'rows' => 3,
                        'class' => 'form-control'
                    )); ?>

                    <br/>

                    <?= $this->Form->button('Submit', array(
                        'class' => 'btn btn-primary btn-md'
                    )); ?>

                    <?= $this->Form->end(); ?>

                </div>

            </div>
        </div>

    </div>
 <hr/>


    </div>



</div> <!-- container -->

