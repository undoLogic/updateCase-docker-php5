<?php $this->SetupCase->loadPageBySlug('contact'); ?>
<div class="global indent contact-page">


    <style>
        textarea {
            color: black!important;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4">

                <?php $this->SetupCase->setTestContent('Address'); ?>
                <h2><?= $this->SetupCase->getTextOnlyBy('Row2-Main', 'title'); ?></h2>

                <div class="info">


                    <?php $this->SetupCase->setTestContent("<h3>Banana Public Relations and Event Management, LLC </h3>
                    <p>New York, U.S.A.</p>
                    <p><span>Telephone:</span>+1.212.537.6593</p>
                    <p><span>Facsimile:</span>+1.212.428.6772</p>
                    <p>E-mail: <a href='mailto:info@bananapr.com'>info@bananapr.com</a></p>"); ?>
                    <?= $this->SetupCase->getContentBy('Main', 'content'); ?>



                </div>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8 contact-form-box">
                <h2>
                    <?= $this->SetupCase->getTextOnlyBy('Main', 'title-form'); ?>

                </h2>


                <?php if (isset($hideForm)): ?>


                    <?= $this->SetupCase->getContentBy('Main', 'after_submit_form_message'); ?>

                    <?php else: ?>

                <form class="form-bananapr" method="post" action="<?= $this->webroot; ?>contacts/index" novalidate>
                    <div class="contact-form-loader"></div>
                    <fieldset>

                        <label class="name form-div-1">
                            <?= $this->Form->input('Contact.name', array(
                                'placeholder' => 'Name*',
                                'label' => false
                            )); ?>
                        </label>
                        <label class="email form-div-2">

                            <?= $this->Form->input('Contact.email', array(
                                'placeholder' => 'Email*',
                                'label' => false,
                                'type' => 'text'
                            )); ?>


                        </label>
                        <label class="phone form-div-3">
                            <?= $this->Form->input('Contact.phone', array(
                                'placeholder' => 'Phone*',
                                'type' => 'text',
                                'label' => false
                            )); ?>

                        </label>
                        <label class="message form-div-4">

                            <?= $this->Form->input('Contact.message', array(
                                'placeholder' => 'Message*',
                                'label' => false
                            )); ?>

                        </label>
                        <label class="message form-div-4">

                            <?= $this->Form->input('Contact.validation', array(
                                'placeholder' => 'Validation: What is 5 plus 3 =',
                                'label' => false
                            )); ?>


                        </label>
                        <!-- <label class="recaptcha"><span class="empty-message">*This field is required.</span></label> -->
                        <div>
                            <?= $this->Form->button('Submit', array(
    'type' => 'submit',
                                'class' => 'btn-default btn3'
)); ?>

                            <p>* required fields</p>
                        </div>
                    </fieldset>
                    <div class="modal fade response-message">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Modal title</h4>
                                </div>
                                <div class="modal-body">
                                    You message has been sent! We will be in touch soon.
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <?php endif; //hide form ?>
            </div>
        </div>
    </div>


</div>
