<div id="WinnerForm" class="section">
	<div class="wrapper">
		<h2>Formulář pro výherce</h2>

		<?php
		$form = new Form('FormWinner');
		$form->LoadFromTmp();

		$code = value($form->data, 'code', '');
		$firstname = value($form->data, 'firstname', '');
		$lastname = value($form->data, 'lastname', '');
		$phone = value($form->data, 'phone', '');
		$email = value($form->data, 'email', '');
		$street = value($form->data, 'street', '');
		$city = value($form->data, 'city', '');
		$zip = value($form->data, 'zip', '');
		$birthday = value($form->data, 'birthday', '');
		?>
		<form action="/action/winner" method="post" enctype="multipart/form-data" class="form" id="FormWinner">
			<div class="form-msg">
				<?php $active = $form->GetMessageClass(Winner::SUCCESS); ?>
				<span class="success<?php ehtml($active); ?>">Formulář byl úspěšně odeslaný, děkujeme.</span>
				<?php $active = $form->GetMessageClass(Winner::E_SAVE_FAILED); ?>
				<span class="error<?php ehtml($active); ?>">Při ukládaní dat nastala chyba. Zkuste to prosím znovu. Pokud chyba přetrvává, kontaktujte prosím technickou podporu.</span>
			</div>
			<div class="row">
				<div class="col-4">
					<div class="form-group">
						<label class="form-label">Výherní kód</label>
						<input type="text" name="code" value="<?php ehtml($code); ?>" class="form-control" placeholder="Sem napište SMS kód ...">
						<div class="form-msg">
							<?php $active = $form->GetMessageClass(Winner::E_CODE_EMPTY); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_CODE_EMPTY); ?>">Nebyl zadaný kód z SMS.</span>
							<?php $active = $form->GetMessageClass(Winner::E_CODE_INVALID); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_CODE_INVALID); ?>">Kód z SMS není správný nebo již byl použit.</span>
						</div>
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label class="form-label">Jméno</label>
						<input type="text" name="firstname" value="<?php ehtml($firstname); ?>" class="form-control" placeholder="Jméno ..." maxlength="64">
						<div class="form-msg">
							<?php $active = $form->GetMessageClass(Winner::E_FIRSTNAME_EMPTY); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_FIRSTNAME_EMPTY); ?>">Nebylo zadáno jméno.</span>
						</div>
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label class="form-label">Příjmení</label>
						<input type="text" name="lastname" value="<?php ehtml($lastname); ?>" class="form-control" placeholder="Příjmení ..." maxlength="64">
						<div class="form-msg">
							<?php $active = $form->GetMessageClass(Winner::E_LASTNAME_EMPTY); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_LASTNAME_EMPTY); ?>">Nebylo zadáno příjmení.</span>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-4">
					<div class="form-group">
						<label class="form-label">Ulice a číslo popisné</label>
						<input type="text" name="street" value="<?php ehtml($street); ?>" class="form-control" placeholder="Ulice a číslo popisné ..." maxlength="64">
						<div class="form-msg">
							<?php $active = $form->GetMessageClass(Winner::E_STREET_EMPTY); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_STREET_EMPTY); ?>">Nebyla zadána ulice a číslo popisné.</span>
						</div>
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label class="form-label">Město</label>
						<input type="text" name="city" value="<?php ehtml($city); ?>" class="form-control" placeholder="Město ..." maxlength="64">
						<div class="form-msg">
							<?php $active = $form->GetMessageClass(Winner::E_CITY_EMPTY); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_CITY_EMPTY); ?>">Nebylo zadáno město.</span>
						</div>
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label class="form-label">PSČ</label>
						<input type="text" name="zip" value="<?php ehtml($zip); ?>" class="form-control" placeholder="PSČ ..." maxlength="6">
						<div class="form-msg">
							<?php $active = $form->GetMessageClass(Winner::E_ZIP_EMPTY); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_ZIP_EMPTY); ?>">Nebylo zadáno PSČ.</span>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-4">
					<div class="form-group">
						<label class="form-label">Datum narození</label>
						<input type="text" name="birthday" value="<?php ehtml($birthday); ?>" class="form-control" placeholder="Datum narození (DD.MM.RRRR)" maxlength="10">
						<div class="form-msg">
							<?php $active = $form->GetMessageClass(Winner::E_BIRTHDAY_EMPTY); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_BIRTHDAY_EMPTY); ?>">Nebylo zadáno datum narození.</span>
							<?php $active = $form->GetMessageClass(Winner::E_BIRTHDAY_INVALID); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_BIRTHDAY_INVALID); ?>">Zadané datum narození není platné. Zadejte jej ve formátu DD.MM.RRRR</span>
						</div>
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label class="form-label">Telefonní číslo</label>
						<input type="text" name="phone" value="<?php ehtml($phone); ?>" class="form-control" placeholder="Telefonní číslo ..." maxlength="14">
						<div class="form-msg">
							<?php $active = $form->GetMessageClass(Winner::E_PHONE_EMPTY); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_PHONE_EMPTY); ?>">Nebylo zadáno telefonní číslo.</span>
							<?php $active = $form->GetMessageClass(Winner::E_PHONE_INVALID); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_PHONE_INVALID); ?>">Zadané telefonní číslo není platné. Zadejte číslo vč. předvolby +420.</span>
							<?php $active = $form->GetMessageClass(Winner::E_PHONE_EXISTS); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_PHONE_EXISTS); ?>">Zadané telefonní číslo již existuje.</span>
						</div>
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						<label class="form-label">E-mailová adresa</label>
						<input type="text" name="email" value="<?php ehtml($email); ?>" class="form-control" placeholder="E-mailová adresa ..." maxlength="64">
						<div class="form-msg">
							<?php $active = $form->GetMessageClass(Winner::E_EMAIL_EMPTY); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_EMAIL_EMPTY); ?>">Nebyla zadána e-mailová adresa.</span>
							<?php $active = $form->GetMessageClass(Winner::E_EMAIL_INVALID); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_EMAIL_INVALID); ?>">Zadaná e-mailová adresa není platná.</span>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-4">
					<div class="form-group">
						<label class="form-label">Účtenka</label>
						<label class="btn-filepicker">
							Nahrajte účtenku
							<input type="file" name="files" style="display: none" />
						</label>
						<div class="form-msg">
							<?php $active = $form->GetMessageClass(Winner::E_FILE_EMPTY); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_FILE_EMPTY); ?>">Nebyla nahrána účtenka.</span>
							<?php $active = $form->GetMessageClass(Winner::E_FILE_OVERSIZED); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_FILE_OVERSIZED); ?>">Soubor s účtenkou je příliš velký.</span>
							<?php $active = $form->GetMessageClass(Winner::E_FILE_MOVE_ERROR); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_FILE_MOVE_ERROR); ?>">Při ukládání souboru nastala chyba.</span>
							<?php $active = $form->GetMessageClass(Winner::E_FILE_UNKNOWN_ERROR); ?>
							<span class="error<?php ehtml($active); ?>" id="<?php ehtml(Winner::E_FILE_UNKNOWN_ERROR); ?>">Při nahrávání souboru nastala neznámá chyba.</span>
						</div>
					</div>
				</div>
				<div class="col-8">
					<div class="selected-files"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="form-group">
						<button type="submit" class="btn">Odeslat</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
