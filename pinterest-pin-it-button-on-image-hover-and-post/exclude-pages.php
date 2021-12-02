<?php
defined('ABSPATH') or die();
wp_register_script('weblizar-for-exclude-page', '', array(), false, true);
wp_enqueue_script('weblizar-for-exclude-page');
?>
<hr>
<div>
	<h3><?php esc_html_e('To Exclude the page(s) from Pin Button On Image Hover. Please add page(s) name below:', WEBLIZAR_PINIT_TD); ?>
	</h3>
	<br>
	<div class="row">
		<div class="col-md-6">
			<input id="no-pin-image-page" name="no-pin-image-page" type="text" value="" placeholder="<?php esc_attr_e('Enter Page Name', WEBLIZAR_PINIT_TD); ?>" class="form-control">			
		</div>
		<div class="col-md-2">
			<?php wp_nonce_field('pinit_exclude_page_nonce_action', 'pinit_exclude_page_nonce_field'); ?>
			<button id="add-pin-page-name" name="add-pin-page-name" class="btn btn-danger" type="button"
			onclick="return SaveNoPinPage(this.value);"><strong><i class="fas fa-plus"></i>&nbsp;&nbsp;<?php esc_html_e('Add', WEBLIZAR_PINIT_TD); ?></strong></button>
		</div>
	</div>
	
	
	<i id="loading-2" name="loading-2" style="display: none;" class="fas fa-cog fa-spin fa-2x"></i>
	
</div>
<?php
$all_excluded_pages = null;
$all_excluded_pages = get_option('excluded_pint_it_pages');
?>
<table class="table mt-5">
	<thead class="thead-dark">
		<tr>
			<th scope="col">#</th>
			<th scope="col"><?php esc_html_e('Page', WEBLIZAR_PINIT_TD); ?>
			</th>
			<th scope="col" class="text-center"><input type="checkbox" id="select-page-all" name="select-page-all[]"
					value="1" /></th>
		</tr>
	</thead>
	<tbody>
		<?php
            if (is_array($all_excluded_pages) && count($all_excluded_pages)) {
                $count = 1;
                foreach ($all_excluded_pages as $exclude_key => $exclude_page) {
                    if ($exclude_page) {
                        ?>
		<tr
			id="<?php echo "page-".$exclude_key; ?>">
			<th scope="row"><?php echo esc_html($count); ?>
			</th>
			<td><?php echo esc_html($exclude_page); ?>
			</td>
			<th scope="col" class="text-center"><input type="checkbox" id="select-page-all" name="select-page-all[]"
					value="<?php echo esc_attr($exclude_key); ?>" />
			</th>
		</tr>
		<?php
                        $count++;
                    }
                }
            } else {
                echo '<tr><td colspan="3">No Page(s) Added Yet.</td></tr>';
            }
        ?>
	</tbody>
	<thead class="thead-dark">
		<tr>
			<th scope="col">#</th>
			<th scope="col"><?php esc_html_e('Page', WEBLIZAR_PINIT_TD); ?>
			</th>
			<th scope="col" class="text-center"><input type="checkbox" id="select-page-all" name="select-page-all[]"
					value="-1" /></th>
		</tr>
	</thead>
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th class="text-center"><button type="button" id="delete-page-all" name="delete-page-all" title="Delte All"
				onclick="return DeleteAll();"><i class="fas fa-trash"></i></button></th>
	</tr>
</table>

<?php
    $js = '';
    $js .= 'jQuery("#select-page-all").click(function(){
	    jQuery("input:checkbox").not(this).prop("checked", this.checked);
	});';
    $js .= '
	function SaveNoPinPage() { 
		var page_name = jQuery("#no-pin-image-page").val();
		if( !page_name ) { 
			jQuery("#no-pin-image-page").focus();
		}

		jQuery("button#add-pin-page-name").hide();
		jQuery("#loading-2").show();
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: "exclude_page", 
				page_name: page_name,
				pinit_exclude_page_nonce_field: jQuery("input#pinit_exclude_page_nonce_field").val(),
			},
			dataType: "html",
			complete : function() {  },
			success: function(data) {
				jQuery("#loading-2").hide();
				jQuery("button#add-pin-page-name").show();
				jQuery("#no-pin-image-page").val("");
				jQuery("#no-pin-image-page").focus();
				location.reload(true);
			}
		});
	}
	';
    $js .= '';
    $js .= 'function DeleteAll(){
		var page_ids = [];
		jQuery(":checkbox:checked").each(function(i){
		  page_ids[i] = jQuery(this).val();
		});
		console.log(page_ids);
		console.log(page_ids.length);
		if(page_ids.length){
			
			jQuery.each( page_ids, function( key, value ) {
				jQuery("#page-"+value).fadeOut(1500);
			});
			
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: "delete_exclude_pages", 
					page_ids: page_ids,
					pinit_exclude_page_nonce_field: jQuery("input#pinit_exclude_page_nonce_field").val(),
				},
				dataType: "html",
				complete : function() { },
				success: function(data) {

				}
			});
		}
	}';
    wp_add_inline_script('weblizar-for-exclude-page', $js);
