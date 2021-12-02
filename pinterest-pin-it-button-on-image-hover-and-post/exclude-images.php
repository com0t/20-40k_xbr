<hr>
<div>
    <h3><?php esc_html_e('To Exclude the image(s) from Pin Button On Image Hover. Please add image(s) URL below:', WEBLIZAR_PINIT_TD); ?>
    </h3>
    <br>
    <div class="row">
        <div class="col-md-6">
            <input id="no-pin-image-url" name="no-pin-image-url" class="form-control" type="text" value="" placeholder="<?php esc_attr_e('Enter Image SRC URL', WEBLIZAR_PINIT_TD); ?>"> 
        </div>
        <div class="col-md-2">
            <?php wp_nonce_field('pinit_exclude_nonce_action', 'pinit_exclude_nonce_field'); ?>
            <button id="add-pin-image-url" name="add-pin-image-url" class="btn btn-danger" type="button"
            onclick="return SaveNoPinImage(this.value);"><strong><i class="fas fa-plus"></i>&nbsp;&nbsp;<?php esc_html_e('Add', WEBLIZAR_PINIT_TD); ?></strong></button>
        </div>
    </div>   
    
    <i id="loading-2" name="loading-2" style="display: none;" class="fas fa-cog fa-spin fa-2x"></i>
    
</div>
<hr>
<div>
    <?php
    $all_exclude_images = null;
    $all_exclude_images = get_option('exclude_pin_it_images');
    //print_r($all_exclude_images);

    ?>
    <table class="table mt-5">
        <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col"><?php esc_html_e('URL', WEBLIZAR_PINIT_TD); ?>
                </th>
                <th scope="col" class="text-center"><input type="checkbox" id="select-all" name="select-all[]"
                        value="-1" /></th>
            </tr>
        </thead>
        <tbody>
            <?php
        //if($all_exclude_images != ''){
            if (is_array($all_exclude_images) && count($all_exclude_images)) {
                $count = 1;
                foreach ($all_exclude_images as $exclude_key => $exclude_image) {
                    if ($exclude_image) {
                        ?>
            <tr id="<?php echo esc_attr($exclude_key); ?>">
                <th scope="row"><?php echo esc_html($count); ?>
                </th>
                <td><?php echo esc_html($exclude_image); ?>
                </td>
                <th scope="col" class="text-center"><input type="checkbox" id="select-all" name="select-all[]"
                        value="<?php echo esc_attr($exclude_key); ?>" />
                </th>
            </tr>
            <?php
                    $count++;
                    }
                }
            }
        //}
         else {
             echo '<tr><td colspan="3">No URL(s) Added Yet.</td></tr>';
         }
        ?>
        </tbody>
        <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col"><?php esc_html_e('URL', WEBLIZAR_PINIT_TD); ?>
                </th>
                <th scope="col" class="text-center"><input type="checkbox" id="select-all" name="select-all[]"
                        value="-1" /></th>
            </tr>
        </thead>
        <tr>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th class="text-center"><button type="button" id="delete-all" name="delete-all" title="Delte All"
                    onclick="return DeleteAllImg();"><i class="fas fa-trash"></i></button></th>
        </tr>

    </table>
</div>
<?php
    wp_register_script('weblizar-for-exclude-images', '', array(), false, true);
    wp_enqueue_script('weblizar-for-exclude-images');

    $js = '';
    
    $js .= ' ';
    $js .= 'jQuery("#select-all").click(function(){';
    $js .= '	jQuery("input:checkbox").not(this).prop("checked", this.checked);';
    $js .= '});';
    $js .= '';
    $js .= ' ';
    $js .= 'function DeleteAllImg(){';
    $js .= '	var img_ids = [];';
    $js .= '	jQuery(":checkbox:checked").each(function(i){';
    $js .= '		img_ids[i] = jQuery(this).val();';
    $js .= '	});';
    $js .= '	console.log(img_ids);';
    $js .= '	console.log(img_ids.length);';
    $js .= '	if(img_ids.length){';
    $js .= '		 ';
    $js .= '		jQuery.each( img_ids, function( key, value ) {';
    $js .= '			jQuery("#"+value).fadeOut(1500);';
    $js .= '		});';
    $js .= '		jQuery.ajax({';
    $js .= '			type: "POST",';
    $js .= '			url: ajaxurl,';
    $js .= '			data: {';
    $js .= '			action: "delete_exclude_images",';
    $js .= '			img_ids: img_ids,';
    $js .= '			pinit_exclude_nonce_field: jQuery("input#pinit_exclude_nonce_field").val(),';
    $js .= '			},';
    $js .= '			dataType: "html",';
    $js .= '			complete : function() {  },';
    $js .= '			success: function(data) {}';
    $js .= '		});';
    $js .= '	}';
    $js .= ' }';
    $js .= 'function SaveNoPinImage(){';
    $js .= '	var img_url = jQuery("#no-pin-image-url").val();';
    $js .= '	if(!img_url) {';
    $js .= '		jQuery("#no-pin-image-url").focus();';
    $js .= '		return false;';
    $js .= '	}';
    $js .= '	jQuery("button#add-pin-image-url").hide();';
    $js .= '	jQuery("#loading-2").show();';
    $js .= '	jQuery.ajax({';
    $js .= '		type: "POST",';
    $js .= '		url: ajaxurl,';
    $js .= '		data: {';
    $js .= '			action: "exclude_image",';
    $js .= '			img_url: img_url,';
    $js .= '			pinit_exclude_nonce_field: jQuery("input#pinit_exclude_nonce_field").val(),';
    $js .= '		},';
    $js .= '		dataType: "html",';
    $js .= '		complete : function() {  },';
    $js .= '		success: function(data) {';
    $js .= '			jQuery("#loading-2").hide();';
    $js .= '			jQuery("button#add-pin-image-url").show();';
    $js .= '			jQuery("#no-pin-image-url").val("");';
    $js .= '			jQuery("#no-pin-image-url").focus();';
    $js .= '            location.reload(true);';
    $js .= '		}';
    $js .= '	});';
    $js .= '}';
    
    wp_add_inline_script('weblizar-for-exclude-images', $js);
