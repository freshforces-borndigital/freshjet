<?php
defined('ABSPATH') or die('Can\'t access directly');

$user  = wp_get_current_user();
$intro = get_field('fcp__intro', 'option');

$find = [
	'{first_name}',
	'{last_name}',
	'{user_email}'
];

$replace = [
	$user->first_name,
	$user->last_name,
	$user->user_email
];

$intro = str_ireplace($find, $replace, $intro);
?>

<div id="reset-password">
	<div class="table">
		<div class="tablecell table-cell">

			<div id="rp-content">
				<div class="rp-inner">
					<?=$intro?>

					<!-- form -->
					<form id="reset-password-form">
						<div class="inputgroup">
							<label>
								<span><?=__('Oud wachtwoord', 'fcp')?></span>
								<input type="password" name="old_password" data-validetta="required">
							</label>
						</div>
						<h4><?=__('Stel een nieuw wachtwoord in', 'fcp')?></h4>
						<div class="inputgroup">
							<label>
								<span><?=__('Nieuw wachtwoord', 'fcp')?></span>
								<input type="password" name="password" data-validetta="required,minLength[9],regExp[min1lowercase],regExp[min1uppercase],regExp[min1number],regExp[min1special]">
							</label>
						</div>
						<div class="inputgroup">
							<label>
								<span><?=__('Herhaal wachtwoord', 'fcp')?></span>
								<input type="password" name="repassword" data-validetta="required,equalTo[password]">
							</label>
						</div>

						<div class="inputgroup">
							<button class="btn btn-primary" name="submit"><?=__('Wachtwoord opslaan', 'fcp')?></button>
						</div>
					</form>
					<!-- /form -->

				</div>
			</div>

		</div>
	</div>
</div>
