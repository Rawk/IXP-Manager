<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'headers' ) ?>
    <style>
        .checkbox input[type=checkbox]{
            margin-left: 0px;
        }

        .col-lg-offset-2{
            margin-left: 0px;
        }

        .checkbox{
            text-align: center;
        }

        #table-core-link tr td{
            vertical-align: middle;
        }
    </style>
<?php $this->append() ?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'core-bundle/list' )?>">Core Bundles</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Edit Core bundle</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route( 'core-bundle/list' )?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="glyphicon glyphicon-plus"></i> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a href="<?= action( 'Interfaces\CoreBundleController@addWizard' )?>" >
                        Add Core Bundle Wizard...
                    </a>
                </li>
            </ul>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <?= $t->alerts() ?>
    <div class="well col-sm-12">
        <?= Former::open()->method( 'POST' )
            ->id( 'core-bundle-form' )
            ->action( action ( 'Interfaces\CoreBundleController@storeWizard' ) )
            ->customWidthClass( 'col-sm-6' )
        ?>
            <div class="col-sm-12">
                <h3>
                    General Core Bundle Settings :
                </h3>
                <hr>
                <div class="col-sm-6">
                    <?= Former::select( 'customer' )
                        ->label( 'Customer' )
                        ->fromQuery( $t->customers, 'name' )
                        ->placeholder( 'Choose a customer' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( '' );
                    ?>

                    <?= Former::text( 'description' )
                        ->label( 'Description' )
                        ->placeholder( 'Description' )
                        ->blockHelp( 'help text' );
                    ?>

                    <?= Former::text( 'graph-title' )
                        ->label( 'Graph Title' )
                        ->placeholder( 'Graph Title' )
                        ->blockHelp( 'help text' );
                    ?>

                    <?= Former::select( 'type' )
                        ->label( 'Type<sup>*</sup>' )
                        ->fromQuery( $t->types, 'name' )
                        ->placeholder( 'Choose Core Bundle type' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( '' )
                        ->value( Entities\CoreBundle::TYPE_ECMP )
                        ->disabled( true );
                    ?>

                </div>

                <div class="col-sm-6">
                    <?= Former::number( 'cost' )
                        ->label( 'Cost' )
                        ->placeholder( '10' )
                        ->min( 0 )
                        ->blockHelp( 'help text' );
                    ?>

                    <?= Former::checkbox( 'enabled' )
                        ->id( 'enabled' )
                        ->label( 'Enabled' )
                        ->unchecked_value( 0 )
                        ->checked_value( 1 )
                        ->blockHelp( "" );
                    ?>

                    <?php if( $t->cb->getType() == Entities\CoreBundle::TYPE_L3_LAG ): ?>
                        <?= Former::checkbox( 'bfd' )
                            ->label( 'BFD' )
                            ->unchecked_value( 0 )
                            ->value( 0 )
                        ?>

                        <?= Former::text( 'subnet' )
                            ->label( 'SubNet' )
                            ->placeholder( '192.0.2.0/30' )
                        ?>
                    <?php endif; ?>
                </div>
                <div style="clear: both"></div>
                <?=Former::actions(
                    Former::primary_submit( 'Save Changes' )->id( 'core-bundle-submit-btn' ),
                    Former::default_link( 'Cancel' )->href( action( 'Interfaces\CoreBundleController@list' ) ),
                    Former::success_button( 'Help' )->id( 'help-btn' )
                )->class('text-center')?>

            </div>
        <?= Former::close() ?>
    </div>

    <div class="row-fluid">
        <h3>
            Virtual Interfaces
        </h3>
        <div class="" id="area-vi">
            <table id="table-virtual-interface" class="table table-bordered">
                <?php foreach( $t->cb->getVirtualInterfaces() as $side => $vi ) :
                    /** @var Entities\VirtualInterface $vi */ ?>
                    <tr>
                        <td>
                            Side <?= $side ?>
                        </td>
                        <td>
                            <?= $vi->getName() ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn btn-default" href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $vi->getId() ] )?>" title="Edit">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div class="row-fluid">
        <div class="panel panel-default">
            <div class="panel-body">

                <div id="message-cl"></div>

                <h3>
                    Core Links
                    <?php if( $t->cb->sameSwitchForEachPIFromCL( true ) && $t->cb->sameSwitchForEachPIFromCL( false ) ): ?>
                        <button style="float: right; margin-right: 20px" id="add-new-core-link" type="button" class=" btn-xs btn btn-default" href="#" title="Add Core link">
                            <span class="glyphicon glyphicon-plus"></span>
                        </button>
                    <?php endif;?>
                </h3>
                <div class="" id="area-cl">
                    <?= Former::open()->method( 'POST' )
                        ->id( 'core-link-form' )
                        ->action( action ( 'Interfaces\CoreBundleController@storeCoreLinks', [ 'id' => $t->cb->getId() ] ) )
                        ->customWidthClass( 'col-sm-10' )
                    ?>
                        <table id="" class="table table-bordered">
                            <tr class="active">
                                <td>
                                    Switch A :
                                    <?php if( $t->cb->sameSwitchForEachPIFromCL( true ) ): ?>
                                        <?= $t->cb->getSwitchSideX( true )->getName() ?>
                                        <input type="hidden" value="<?= $t->cb->getSwitchSideX( true )->getId() ?>" id="switch-a">
                                    <?php else: ?>
                                        <span class="label label-warning">Multiple</span>
                                    <?php endif;?>
                                </td>
                                <td>
                                    Switch B :
                                    <?php if( $t->cb->sameSwitchForEachPIFromCL( false ) ): ?>
                                        <?= $t->cb->getSwitchSideX( false )->getName() ?>
                                        <input type="hidden" value="<?= $t->cb->getSwitchSideX( false )->getId() ?>" id="switch-b">
                                    <?php else: ?>
                                        <span class="label label-warning">Multiple</span>
                                    <?php endif;?>
                                </td>
                            </tr>
                        </table>
                        <table id="table-core-link" class="table table-bordered">
                            <tr>
                                <th>
                                    Number
                                </th>
                                <th>
                                    Switch Port A
                                </th>
                                <th>
                                    Switch Port B
                                </th>
                                <th>
                                    Enabled
                                </th>
                                <?php if( $t->cb->isECMP () ): ?>
                                    <th>
                                        BFD
                                    </th>
                                    <th>
                                        Subnet
                                    </th>
                                <?php endif; ?>
                                <th>
                                    Action
                                </th>
                            </tr>
                            <?php $nbCl = 1 ?>
                            <?php foreach( $t->cb->getCoreLinks() as $cl ) :
                                /** @var Entities\CoreLink $cl */ ?>
                                <tr>
                                    <td style="vertical-align: middle">
                                        <?= $nbCl ?>
                                    </td>
                                    <td>
                                        <?= $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getSwitchPort()->getName() ?>
                                        <a class="btn btn-sm btn-default" href="<?= route('interfaces/physical/edit/from-core-bundle' , [ 'id' => $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getId(), 'cb' => $t->cb->getId() ] ) ?>"><i class="glyphicon glyphicon-pencil"></i></a>
                                    </td>
                                    <td>
                                        <?= $cl->getCoreInterfaceSideB()->getPhysicalInterface()->getSwitchPort()->getName() ?>
                                        <a class="btn btn-sm btn-default" href="<?= route('interfaces/physical/edit' , [ 'id' => $cl->getCoreInterfaceSideB()->getPhysicalInterface()->getId() ] ) ?>"><i class="glyphicon glyphicon-pencil"></i></a>
                                    </td>
                                    <td>
                                        <?= Former::checkbox( 'enabled-'.$cl->getId() )
                                            ->label( '' )
                                            ->unchecked_value( 0 )
                                            ->check($cl->getEnabled() ? true : false)
                                        ?>
                                    </td>
                                    <?php if( $t->cb->isECMP () ): ?>
                                        <td>
                                            <?= Former::checkbox( 'bfd-'.$cl->getId() )
                                                ->label( '' )
                                                ->unchecked_value( 0 )
                                                ->check($cl->getBFD() ? true : false)
                                            ?>

                                        </td>
                                        <td>
                                            <?= Former::text( 'subnet-'.$cl->getId() )
                                                ->label( '' )
                                                ->placeholder( '192.0.2.0/30' )
                                                ->value( $cl->getIPv4Subnet() )
                                                ->class( 'subnet-cl form-control' )
                                            ?>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <?php if( count( $t->cb->getCoreLinks() ) > 1 ): ?>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a class="btn btn btn-default" id="delete-cl-<?=  $cl->getId() ?>" href="#" title="Edit">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php $nbCl++ ?>
                            <?php endforeach; ?>
                        </table>



                        <?=Former::actions(
                            Former::primary_submit( 'Save Changes' )->id( 'core-links-submit-btn' )
                        )->class('text-center');?>

                    <?= Former::close() ?>
                </div>
            </div>
        </div>
    </div>

    <div id="core-links-area" class="col-sm-12" style="opacity: 0; margin-bottom: 20px" >
        <?= Former::open()->method( 'POST' )
            ->id( 'core-link-form' )
            ->action( action ( 'Interfaces\CoreBundleController@addCoreLink' ) )
            ->customWidthClass( 'col-sm-6' )
        ?>
        <div id="core-links">

        </div>

        <?= Former::hidden( 'nb-core-links' )
            ->id( 'nb-core-links')
            ->value( 0 )
        ?>

        <?= Former::hidden( 'core-bundle' )
            ->id( 'core-bundle')
            ->value( $t->cb->getId() )
        ?>

        <?=Former::actions(
            Former::primary_submit( 'Add new core link' )->id( 'new-core-links-submit-btn' )
        )->class('text-center');?>

        <?= Former::close() ?>
    </div>

    <div class="col-sm-12 alert alert-danger" style="float: right;" role="alert">
        <div>
            <span style="line-height: 34px;">
                <strong>Text ....</strong>
            </span>
            <a class="btn btn btn-danger" onclick="deleteCoreBundle()" style="float: right;" title="Delete">
                Delete
            </a>
        </div>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script type="text/javascript" src="<?= asset( '/bower_components/ip-address/dist/ip-address-globals.js' ) ?>"></script>
    <script>
        $(document).ready( function() {
            /**
             * hide the help block at loading
             */
            $('p.help-block').hide();
            $('div.help-block').hide();

            /**
             * display / hide help sections on click on the help button
             */
            $( "#help-btn" ).click( function() {
                $( "p.help-block" ).toggle();
                $( "div.help-block" ).toggle();
            });
        });

        /**
         * check if the subnet is valid
         */
        $( "input[id|='subnet']" ).blur( function() {
            checkSubnet(this.id);
        });

        /**
         * check if the subnet is valid and display a message
         */
        function checkSubnet( subnet ){
            $( "#"+subnet ).parent().parent().removeClass( 'has-error' );
            if( $( "#"+subnet ).val() != '' ){
                $( "#"+subnet ).parent().find('span').remove();
                if( !validSubnet( $( "#"+subnet ).val() ) ){
                    $( "#"+subnet ).parent().parent().addClass( 'has-error' );
                    $( "#"+subnet ).parent().append("<span class='help-block'>The subnet is not valid</span> ");
                }
                else{
                    $( "#"+subnet ).parent().parent().addClass( 'has-success' );
                    $( "#"+subnet ).parent().append("<span class='help-block'>The subnet is valid</span> ");
                }
            }
        }

        /**
         * Check if the subnet provided is valid
         */
        function validSubnet( subnet ){
            var address = new Address4( subnet );
            if( address.isValid() ){
                return true;
            } else {
                return false;
            }
        }

        $( "a[id|='delete-cl']" ).on( 'click', function(e){
            e.preventDefault();
            var clid = (this.id).substring(10);
            deleteCoreLink( clid );
        });

        /**
         * Check if all the switch ports have been chosen before submit
         */
        $('#core-link-form').submit(function( e ) {
            $( ".subnet-cl" ).each(function() {
                if( $( this ).val() != '' ){
                    if( !validSubnet( $( this ).val() ) ){
                        $("#message-cl").html("<div class='alert alert-danger' role='alert'> The subnet " + $( this ).val() + " is not valid! </div>");
                        e.preventDefault();
                    }
                }

            });

        });

        function deleteCoreLink( clid ){
            urlAction = "<?= url('api/v4/core-link/delete') ?>/" + clid;

            bootbox.confirm({
                title: "Delete Core link",
                message: "Are you sure you want to delete this Core link ?",
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> Cancel'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> Confirm'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $.ajax( urlAction, {
                            type: 'GET'
                        })
                            .done( function( data ) {
                                result = ( data.success ) ? 'success': 'danger';
                                if( result ) {
                                    $("#message-cl").html("<div class='alert alert-" + result + "' role='alert'> Core link deleted with success </div>");
                                    refreshDataTable( );
                                }
                            })
                            .fail( function(){
                                alert( 'Could not update notes. API / AJAX / network error' );
                                throw new Error("Error running ajax query for "+urlAction);
                            })
                            .always( function() {
                                $('#notes-modal').modal('hide');
                            });
                    }
                }
            });
        }

        function deleteCoreBundle( ){
            urlAction = "<?= route('core-bundle/delete', [ 'id' => $t->cb->getId() ]) ?>/";

            bootbox.confirm({
                title: "Delete Core Bundle",
                message: "Are you sure you want to delete this Core Bundle ?",
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> Cancel'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> Confirm'
                    }
                },
                callback: function (result) {
                    if (result) {
                        window.location.href = urlAction;
                    }
                }
            });
        }

        /**
         * allow to refresh the table without reloading the page
         * reloading only a part of the DOM
         */
        function refreshDataTable() {
            $( "#area-cl").load( $(location).attr('pathname')+" #core-link-form" );
        }

        /**
         * add a new link to the core bundle
         */
        $( "#add-new-core-link" ).click( function() {
            addCoreLink( );
        });

        function addCoreLink(){

            if( $( "#enabled").is( ':checked' ) ){
                enabled = 1;
            } else{
                enabled = 0;
            }


            var ajaxCall = $.ajax( "<?= action( 'Interfaces\CoreBundleController@addCoreLinkFrag' ) ?>", {
                data: {
                    nbCoreLink      : 0,
                    enabled         : enabled,
                    bundleType      : $( "#type").val(),
                    _token          : "<?= csrf_token() ?>"
                },
                type: 'POST'
            })
            .done( function( data ) {
                if( data.success ){

                    // add the new core link form
                    $('#core-links').append( data.htmlFrag );

                    $('#core-links-area').css( 'opacity' , '100' );
                    $('#add-new-core-link').attr( 'disabled', 'disabled' );

                    oldNbLink = $("#nb-core-links").val( );
                    setSwitchPort( 'a' );
                    setSwitchPort( 'b' );

                    // set the number of core links present for the core bundle
                    $("#nb-core-links").val( data.nbCoreLinks );
                }

            })
            .fail( function() {
                throw new Error( "Error running ajax query for core-bundle/add-core-link-frag" );
                alert( "Error running ajax query for core-bundle/add-core-link-frag" );
            })
        }


        /**
         * set data to the switch port dropdown when we select a switcher
         */
        function setSwitchPort( sside ){
            switchId = $( "#switch-" + sside ).val();

            $( "#sp-" + sside + "-1" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );
            if( switchId != null && switchId != '' ){

                url = "<?= url( '/api/v4/switch' )?>/" + switchId + "/switch-port";
                $.ajax( url , {
                    type: 'POST'
                })

                .done( function( data ) {
                    var options = "<option value=\"\">Choose a switch port</option>\n";
                    $.each( data.listPorts, function( key, value ){
                        options += "<option value=\"" + value.id + "\">" + value.name + " (" + value.type + ")</option>\n";
                    });
                    $( "#sp-" + sside + "-1" ).html( options );
                })
                .fail( function() {
                    throw new Error( "Error running ajax query for api/v4/switcher/$id/switch-port" );
                    alert( "Error running ajax query for api/v4/switcher/$id/switch-port" );


                })
                .always( function() {
                    $( "#sp-" + sside + "-1" ).trigger( "chosen:updated" );
                });
            }

        }


        /**
         * Check if all the switch ports have been chosen before submit
         */
        $('#new-core-links-submit-btn').click(function() {
            $("#message-1").html('');

            if( !$( "#sp-a-1" ).val() || !$( "#sp-b-1" ).val() ){
                $( "#message-1" ).append( "<div class='alert alert-danger' role='alert'>You need to select switch ports.</div>" );
                return false;
            }

            // check if the subnet is valid
            if( $( "#subnet-1").val() ) {
                subnet = $( "#subnet-1").val();
                if( !validSubnet( subnet ) ){
                    error = true;
                    $( "#message-1" ).append(  "<div class='alert alert-danger' role='alert'>The subnet " + subnet + " is not valid! </div>" );
                    return false;
                }
            }
        });

    </script>
<?php $this->append() ?>