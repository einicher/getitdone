			<div class="row">
				<div class="col-md-12">

					<div class="hero-unit">
						<h1>Welcome!</h1>
						<p>“Get it done.” is a simple todo tool using the todo.txt format. Sign up for free and start organizing!</p>
					</div>

				</div>
			</div>
			<div class="row">
				<div class="col-md-6 login-row-2">

					<div class="hero-unit">
						<h1><?=§('Login')?></h1>
<? if ($login === false) : ?>
						<p class="alert alert-danger"><?=§('Wrong email or password.')?></p>
<? endif; ?>

						<form method="post" action="" class="form-horizontal">
							<div class="form-group">
								<label class="col-md-3 control-label" for="loginEmail"><?=§('Email')?></label>
								<div class="col-md-9">
									<input type="text" id="loginEmail" name="loginEmail" placeholder="Email" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label" for="loginPassword"><?=§('Password')?></label>
								<div class="col-md-9">
									<input type="password" id="loginPassword" name="loginPassword" placeholder="Password" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-offset-3 col-lg-9">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="loginRemember"> <?=§('Remember me')?>
										</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-offset-3 col-lg-9">
									<button type="submit" class="btn btn-primary"><?=§('Sign in')?></button>
									<a href="<?=$this->link->get('users.password')?>" style="font-size: smaller; margin-left: 10px;"><?=§('Forgot password')?></a>
								</div>
							</div>
						</form>
					</div>

				</div>
				<div class="col-lg-6 login-row-2">

					<div class="hero-unit">
						<h1><?=§('Sign up')?></h1>
<? if (empty($signUp) || $signUp === false) : ?>
	<? if ($signUp === false) : ?>
						<p class="alert alert-danger"><?=§('Email %s already exists.', '<b>'.$_POST['signUpEmail'].'</b>')?> <a href="<?=$this->link->get('users.password')?>"><?=§('Forgot your password?')?></a></p>
	<? endif; ?>
						<p><?=§('Sign up here for free by entering a valid email address. An automatically generated password will be sent to you with which you can log in and get started immediately.')?></p>
						<br />
						<form method="post" action="" class="form-horizontal">
							<div class="form-group">
								<label class="col-md-2 control-label" for="signUpEmail"><?=§('Email')?></label>
								<div class="col-md-10">
									<input type="text" id="signUpEmail" name="signUpEmail" placeholder="Email" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-offset-2 col-md-10">
									<button type="submit" class="btn btn-primary"><?=§('Sign up')?></button>
								</div>
							</div>
						</form>
<? else : ?>
						<p><?=§('Your registration has been successful, an automatically generated password was sent to your email address.')?></p>
<? endif; ?>
					</div>

				</div>
			</div>
