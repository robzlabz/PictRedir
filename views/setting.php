<div class="wrap">
	<h2>WP Picture Redirect <small>by RobzLabz</small></h2>
	<form action="" method="POST" id="form">
		<input type="hidden" name="action" value="setting">
		<table class="form-table" id="table">		
			<tr>
				<th><label>Status : </label></th>
				<td>
					<label>
						<input type="checkbox" name="status" <?php if($wpr_status == 1) echo "checked"; ?>> Enable
					</label>
				</td>
			</tr>
			<tr>
				<th><label>Redirect to : </label></th>
				<td>
					<select name="redir" id="redir">
						<option value="home" <?php if($wpr_redir == 'home') echo "selected"; ?>>Home</option>
						<option value="single" <?php if($wpr_redir == 'single') echo "selected"; ?>>Single</option>
						<option value="attachment" <?php if($wpr_redir == 'attachment') echo "selected"; ?>>Attachment</option>
					</select>
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<button type="submit" class="button-primary" id="push">Save</button>
					<button type="submit" class="button-primary" id="cache">Clear Cache</button>
				</td>
			</tr>
		</table>
	</form>	
</div>

<div class="wrap" id="result"></div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#push").click(function(e){
			e.preventDefault();

			$('#result').fadeIn(1000).html('<p class="description"><img src="../wp-admin/images/wpspin_light.gif"> Saving changes....</p>');

			var data = $('#form').serialize();

			$.post(ajaxurl, data, function(result){
				$("#result").html(result).fadeIn(1000); //.fadeOut(5000);
			})
		});

		$("#cache").click(function(e){
			e.preventDefault();
			$('#result').fadeIn(1000).html('<p class="description"><img src="../wp-admin/images/wpspin_light.gif"> Saving changes....</p>');
			var data = { action : 'clear_cache' }
			$.post(ajaxurl, data, function(result){
				$("#result").html(result).fadeIn(1000); 
			})
		});
	});
</script>
