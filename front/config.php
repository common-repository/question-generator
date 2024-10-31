<?php  ?>
<div class="qg_wrap">
<h1>Question Generator <small>by squareMX</small></h1>
<?php if (get_option('qg_validation') != 1) : ?>
    <h3>Please set your API Key and Account Key in order to connect the plugin and Question-Generator Account : <a href="https://www.question-generator.com/account/api" target="_blank">https://www.question-generator.com/account/api</a></h3>
<?php endif; ?>

<div id="qg-panel-overview" class="postbox">
					
					<h2><?php esc_html_e('Overview', 'question-generator'); ?></h2>
					
					<div class="toggle<?php if (isset($_GET['settings-updated'])) echo ' default-hidden'; ?>">
						
						<div class="qg-panel-overview">
							
							<div class="qg-col-1">
								
								<p><?php esc_html_e('This plugin allow you to publish content directly from Question Generator, and much more!', 'question-generator'); ?></p>
								
								<ul>
									<li><a class="qg-toggle" data-target="usage" href="#qg-panel-usage"><?php esc_html_e('How to Use', 'question-generator'); ?></a></li>
									<li><a class="qg-toggle" data-target="settings" href="#qg-panel-settings"><?php esc_html_e('Plugin Settings', 'question-generator'); ?></a></li>
									<li><a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/question-generator/"><?php esc_html_e('Plugin Homepage', 'question-generator'); ?></a></li>
								</ul>
								
								<p>
									<?php esc_html_e('If you like this plugin, please', 'question-generator'); ?> 
									<a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/question-generator/reviews/?rate=5#new-post" title="<?php esc_attr_e('THANK YOU for your support!', 'question-generator'); ?>">
										<?php esc_html_e('give it a 5-star rating', 'question-generator'); ?>&nbsp;&raquo;
									</a>
								</p>
								
							</div>
							
							
							
						</div>
						
					</div>
					
				</div>
    <div id="qg-panel-settings" class="postbox">
        
    <h2><?php esc_html_e('Settings', 'question-generator'); ?></h2>    
        <div class="qg-panel-settings">  
        <p><?php esc_html_e('Status', 'question-generator'); ?>
        <span style="color: green;" <?php 
				
				if(get_option('qg_validation') == 1){
					echo '';
				}else{
					echo 'hidden';
				}  ?>>
				<span class="dashicons dashicons-yes"></span> Connected</span>
                    <span style="color: red;" <?php 
										if(get_option('qg_validation') == 4 || get_option('qg_validation') == -1){
											echo '';
										}else{
											echo 'hidden';
										} ?>><span class="dashicons dashicons-no"></span> Disconnected from Question-Generator</span>
                    <span style="color: red;" <?php 
										if($code_erreur== 3){
											echo '';
										}else{
											echo 'hidden';
										} ?>><span class="dashicons dashicons-no"></span> Website not found</span>
        </p>

				<?php if(get_option('api_qg_key')){
					echo "<p>";
					 esc_html_e('API Key', 'question-generator'); 
					 echo "<span>";
					 esc_html_e(get_option('api_qg_key')); 
					 echo "</span>";
					echo "</p>";
				}
				?> 
            <form name="subId" method="POST" action="">
                <table class="form-table">
                    
                    <tr>
                        <th>Your Api Key</th>
                        <td>
                            <input type="text" name="api_qg_key" id="api_qg_key" placeholder="API key" required
														/>

                            <span style="color: red;" <?php
														if ($code_erreur == 1){
															echo '';
														}else{
															echo "hidden";
														}?>><span class="dashicons dashicons-no"></span> API key not found</span>
                        </td>
                    </tr>
                    
                </table>
                
                <input type="submit" name="submit" id="submit" class="button button-primary" value='Save'/>
            </form>
        </div>
    </div>

    <div id="qg-panel-usage" class="postbox">
        
    <h2><?php esc_html_e('How to Use', 'question-generator'); ?></h2>    
        <div class="qg-panel-settings">  
        <div class="qg-panel-usage">
							
							
							
							<p><?php esc_html_e('How to use this plugin:', 'question-generator'); ?></p>
							
							<ol>
								<li>Create an account on <a href="https://www.question-generator.com/register" target="_blank"> Question-Generator</a> or <a href="https://www.question-generator.com/account" target="_blank">Login</a> into your account panel</li>
								<li>Create  <a href="https://www.question-generator.com/account/api" target="_blank">an API Key </a></li>
								<li>
									<?php esc_html_e('Generate content from Keyword', 'question-generator'); ?> <a target="_blank" rel="noopener noreferrer" href="https://www.question-generator.com/account/keywords/search"><?php esc_html_e('QGKeyword', 'question-generator'); ?></a> 
									
								</li>
								<li><?php esc_html_e('Test your connection from Question-generator', 'question-generator'); ?></li>
							</ol>
							
						</div>
						
        </div>
    </div>

</div>