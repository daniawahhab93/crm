<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('39', 'created');
$edited = can_action('39', 'edited');
$deleted = can_action('39', 'deleted');
if (!empty($created) || !empty($edited)) {
?>
<div class="nav-tabs-custom">
    <?php $is_department_head = is_department_head();
    if ($this->session->userdata('user_type') == 1 || !empty($is_department_head)) { ?>
        <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
             data-title="<?php echo lang('filter_by'); ?>">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                <i class="fa fa-filter" aria-hidden="true"></i>
            </button>
            <ul class="dropdown-menu group animated zoomIn" style="width:300px;">
                <li class="filter_by all_filter"><a href="#"><?php echo lang('all'); ?></a></li>
                <li class="divider"></li>

                <li class="dropdown-submenu pull-left  " id="from_account">
                    <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('group'); ?></a>
                    <ul class="dropdown-menu dropdown-menu-left from_account" style="">
                        <?php if (!empty($all_customer_group)) { ?>
                            <?php foreach ($all_customer_group as $customer_group_id => $customer_group) {
                                ?>
                                <li class="filter_by" id="<?= $customer_group_id ?>" search-type="by_group">
                                    <a href="#"><?php echo $customer_group; ?></a>
                                </li>
                            <?php }
                            ?>
                            <div class="clearfix"></div>
                        <?php } ?>
                    </ul>
                </li>
                <div class="clearfix"></div>
                <li class="dropdown-submenu pull-left " id="to_account">
                    <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('manufacturer'); ?></a>
                    <ul class="dropdown-menu dropdown-menu-left to_account" style="">
                        <?php
                        if (!empty($all_manufacturer)) { ?>
                            <?php foreach ($all_manufacturer as $manufacturer_id => $manufacturer) {
                                ?>
                                <li class="filter_by" id="<?= $manufacturer_id ?>" search-type="by_manufacturer">
                                    <a href="#"><?php echo $manufacturer; ?></a>
                                </li>
                            <?php }
                            ?>
                            <div class="clearfix"></div>
                        <?php } ?>
                    </ul>
                </li>
                <div class="clearfix"></div>
                <li class="dropdown-submenu pull-left " id="by_category">
                    <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('warehouse'); ?></a>
                    <ul class="dropdown-menu dropdown-menu-left by_category" style="">
                        <?php
                        if (!empty($warehouseList)) { ?>
                            <?php foreach ($warehouseList as $warehouseId => $warehouseName) {
                                ?>
                                <li class="filter_by" id="<?= $warehouseId ?>" search-type="by_warehourse">
                                    <a href="#"><?php echo $warehouseName; ?></a>
                                </li>
                            <?php }
                            ?>
                            <div class="clearfix"></div>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
        </div>
    <?php } ?>
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">

        <li class=""><a href="<?= base_url('admin/office_items/items_list') ?>"><?= lang('all_items') ?></a>
        </li>
        <li class="active"><a href="<?= base_url('admin/office_items/new_items') ?>"><?= lang('new_items') ?></a>
        </li>
    </ul>
    <style type="text/css">
        .custom-bulk-button {
            display: initial;
        }
    </style>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->


        <div class="tab-pane active" id="create">
            <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form"
                  action="<?php echo base_url(); ?>admin/office_items/saved_items/<?php
                  if (!empty($items_info)) {
                      echo $items_info->items_id;
                  }
                  ?>" method="post" class="form-horizontal row ">
                <div class="col-sm-7">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('item_name') ?> <span
                                    class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" value="<?php
                            if (!empty($items_info)) {
                                echo $items_info->item_name;
                            }
                            ?>" name="item_name" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 col-md-3 col-sm-3 control-label"><?= lang('cost_price') ?> <span
                                    class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" data-parsley-type="number" class="form-control" value="<?php
                            if (!empty($items_info->cost_price)) {
                                echo $items_info->cost_price;
                            }
                            ?>" name="cost_price" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('quantity') ?> <span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input type="text" data-parsley-type="number" class="form-control" value="<?php
                            if (!empty($items_info)) {
                                echo $items_info->quantity;
                            }
                            ?>" name="quantity" required="">
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
                        <div class="col-lg-9">
                                <textarea name="item_desc" class="form-control textarea_"><?php
                                    if (!empty($items_info)) {
                                        echo $items_info->item_desc;
                                    }
                                    ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('notes') ?> </label>
                        <div class="col-lg-9">
                                <textarea name="notes" class="form-control textarea_"><?php
                                    if (!empty($items_info)) {
                                        echo $items_info->notes;
                                    }
                                    ?></textarea>
                        </div>
                    </div>
                    <?php
                    if (!empty($items_info)) {
                        $saved_items_id = $items_info->saved_items_id;
                    } else {
                        $saved_items_id = null;
                    }
                    ?>

                    <div class="form-group mt-lg">
                        <label class="col-lg-3 control-label"></label>
                        <div class="col-lg-9">
                            <div class="btn-bottom-toolbar">
                                <?php
                                if (!empty($items_info)) { ?>
                                    <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                    <button type="button" onclick="goBack()"
                                            class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                                <?php } else {
                                    ?>
                                    <button type="submit" class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                                <?php }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 pull-right">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <?= lang('image') ?> </div>
                        <div class="panel-body">

                            <div id="comments_file-dropzone" class="dropzone mb15">

                            </div>
                            <div id="comments_file-dropzone-scrollbar">
                                <div id="comments_file-previews">
                                    <div id="file-upload-row" class="mt pull-left">

                                        <div class="preview box-content pr-lg" style="width:100px;">
                                                <span data-dz-remove class="pull-right" style="cursor: pointer">
                                                    <i class="fa fa-times"></i>
                                                </span>
                                            <img data-dz-thumbnail class="upload-thumbnail-sm"/>
                                            <input class="file-count-field" type="hidden" name="files[]" value=""/>
                                            <div class="mb progress progress-striped upload-progress-sm active mt-sm"
                                                 role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                 aria-valuenow="0">
                                                <div class="progress-bar progress-bar-success" style="width:0%;"
                                                     data-dz-uploadprogress></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if (!empty($items_info->upload_file)) {
                                $uploaded_file = json_decode($items_info->upload_file);
                            }
                            if (!empty($uploaded_file)) {
                                foreach ($uploaded_file as $v_files_image) { ?>
                                    <div class="pull-left mt pr-lg mb" style="width:100px;">
                                        <span data-dz-remove class="pull-right existing_image"
                                              style="cursor: pointer"><i class="fa fa-times"></i></span>
                                        <?php if ($v_files_image->is_image == 1) { ?>
                                            <img data-dz-thumbnail src="<?php echo base_url() . $v_files_image->path ?>"
                                                 class="upload-thumbnail-sm"/>
                                        <?php } else { ?>
                                            <span data-toggle="tooltip" data-placement="top"
                                                  title="<?= $v_files_image->fileName ?>"
                                                  class="mailbox-attachment-icon"><i
                                                        class="fa fa-file-text-o"></i></span>
                                        <?php } ?>

                                        <input type="hidden" name="path[]" value="<?php echo $v_files_image->path ?>">
                                        <input type="hidden" name="fileName[]"
                                               value="<?php echo $v_files_image->fileName ?>">
                                        <input type="hidden" name="fullPath[]"
                                               value="<?php echo $v_files_image->fullPath ?>">
                                        <input type="hidden" name="size[]" value="<?php echo $v_files_image->size ?>">
                                        <input type="hidden" name="is_image[]"
                                               value="<?php echo $v_files_image->is_image ?>">
                                    </div>
                                <?php }; ?>
                            <?php }; ?>
                            <script type="text/javascript">
                                $(document).ready(function () {
                                    $(".existing_image").on("click", function () {
                                        $(this).parent().remove();
                                    });

                                    fileSerial = 0;
                                    // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
                                    var previewNode = document.querySelector("#file-upload-row");
                                    previewNode.id = "";
                                    var previewTemplate = previewNode.parentNode.innerHTML;
                                    previewNode.parentNode.removeChild(previewNode);
                                    Dropzone.autoDiscover = false;
                                    var projectFilesDropzone = new Dropzone("#comments_file-dropzone", {
                                        url: "<?= base_url() ?>admin/common/upload_file",
                                        thumbnailWidth: 80,
                                        thumbnailHeight: 80,
                                        parallelUploads: 20,
                                        previewTemplate: previewTemplate,
                                        dictDefaultMessage: '<?php echo lang("file_upload_instruction"); ?>',
                                        autoQueue: true,
                                        previewsContainer: "#comments_file-previews",
                                        clickable: true,
                                        accept: function (file, done) {
                                            if (file.name.length > 200) {
                                                done("Filename is too long.");
                                                $(file.previewTemplate).find(".description-field")
                                                    .remove();
                                            }
                                            //validate the file
                                            $.ajax({
                                                url: "<?= base_url() ?>admin/common/validate_project_file",
                                                data: {
                                                    file_name: file.name,
                                                    file_size: file.size
                                                },
                                                cache: false,
                                                type: 'POST',
                                                dataType: "json",
                                                success: function (response) {
                                                    if (response.success) {
                                                        fileSerial++;
                                                        $(file.previewTemplate).find(
                                                            ".description-field")
                                                            .attr("name", "comment_" +
                                                                fileSerial);
                                                        $(file.previewTemplate).append(
                                                            "<input type='hidden' name='file_name_" +
                                                            fileSerial +
                                                            "' value='" + file
                                                                .name + "' />\n\
                                                                        <input type='hidden' name='file_size_" +
                                                            fileSerial +
                                                            "' value='" + file
                                                                .size + "' />");
                                                        $(file.previewTemplate).find(
                                                            ".file-count-field")
                                                            .val(fileSerial);
                                                        done();
                                                    } else {
                                                        $(file.previewTemplate).find(
                                                            "input").remove();
                                                        done(response.message);
                                                    }
                                                }
                                            });
                                        },
                                        processing: function () {
                                            $("#file-save-button").prop("disabled", true);
                                        },
                                        queuecomplete: function () {
                                            $("#file-save-button").prop("disabled", false);
                                        },
                                        fallback: function () {
                                            //add custom fallback;
                                            $("body").addClass("dropzone-disabled");
                                            $('.modal-dialog').find('[type="submit"]').removeAttr(
                                                'disabled');

                                            $("#comments_file-dropzone").hide();

                                            $("#file-modal-footer").prepend(
                                                "<button id='add-more-file-button' type='button' class='btn  btn-default pull-left'><i class='fa fa-plus-circle'></i> " +
                                                "<?php echo lang("add_more"); ?>" + "</button>");

                                            $("#file-modal-footer").on("click",
                                                "#add-more-file-button",
                                                function () {
                                                    var newFileRow =
                                                        "<div class='file-row pb pt10 b-b mb10'>" +
                                                        "<div class='pb clearfix '><button type='button' class='btn btn-xs btn-danger pull-left mr remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>" +
                                                        "<div class='mb5 pb5'><input class='form-control description-field'  name='comment[]'  type='text' style='cursor: auto;' placeholder='<?php echo lang("comment") ?>' /></div>" +
                                                        "</div>";
                                                    $("#comments_file-previews").prepend(
                                                        newFileRow);
                                                });
                                            $("#add-more-file-button").trigger("click");
                                            $("#comments_file-previews").on("click", ".remove-file",
                                                function () {
                                                    $(this).closest(".file-row").remove();
                                                });
                                        },
                                        success: function (file) {
                                            setTimeout(function () {
                                                $(file.previewElement).find(
                                                    ".progress-striped").removeClass(
                                                    "progress-striped").addClass(
                                                    "progress-bar-success");
                                            }, 1000);
                                        }
                                    });

                                })
                            </script>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <?php } ?>
    </div>