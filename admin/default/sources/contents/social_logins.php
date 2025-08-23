<div class="i_contents_container">
    <div class="i_general_white_board border_one column flex_ tabing__justify">
        <div class="i_general_title_box">
            <?php echo iN_HelpSecure($LANG['social_logins']); ?>
        </div>
        
        <!-- Google OAuth Setup Instructions -->
        <div class="i_general_row_box column flex_" style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; margin-bottom: 20px;">
            <h3>Google OAuth Setup Instructions</h3>
            <p><strong>Step 1:</strong> Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></p>
            <p><strong>Step 2:</strong> Create/select project → APIs & Services → Credentials</p>
            <p><strong>Step 3:</strong> Create OAuth 2.0 Client ID (Web application)</p>
            <p><strong>Step 4:</strong> Add this redirect URI: <code><?php echo iN_HelpSecure($base_url); ?>googleLogin.php</code></p>
            <p><strong>Step 5:</strong> Copy Client ID and Client Secret below</p>
            <p><strong>Step 6:</strong> Enable the status toggle</p>
        </div>

        <div class="i_general_row_box column flex_" id="general_conf">
            <form enctype="multipart/form-data" method="post" id="storageSettings">
                <?php
                $socialLoginList = $iN->iN_SocialLoginsList();
                if ($socialLoginList) {
                    foreach ($socialLoginList as $slData) {
                        $slID = $slData['s_id'] ?? null;
                        $sKey = $slData['s_key'] ?? null;
                        $sKeyOne = $slData['s_key_one'] ?? null;
                        $sKeyTwo = $slData['s_key_two'] ?? null;
                        $sLoginIcon = $slData['s_icon'] ?? null;
                        $sStatus = $slData['s_status'] ?? null;
                ?>
                        <div class="social-login-section" style="border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 5px;">
                            <h3><?php echo ucfirst(iN_HelpSecure($sKey)); ?> Login Configuration</h3>
                            
                            <div class="i_general_row_box_item flex_ tabing_non_justify">
                                <div class="irow_box_left tabing flex_"><?php echo iN_HelpSecure($LANG[$sKey]); ?> CLIENT ID</div>
                                <div class="irow_box_right">
                                    <input type="text" name="<?php echo iN_HelpSecure($sKey); ?>_cliend_id" class="i_input flex_" value="<?php echo iN_HelpSecure($sKeyOne); ?>" placeholder="e.g., 123456789-abcdefg.apps.googleusercontent.com">
                                    <?php if ($sKey === 'google'): ?>
                                        <div class="rec_not box_not_padding_left">
                                            <small>Get this from Google Cloud Console → Credentials → OAuth 2.0 Client IDs</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="i_general_row_box_item flex_ tabing_non_justify">
                                <div class="irow_box_left tabing flex_"><?php echo iN_HelpSecure($LANG[$sKey]); ?> SECRET KEY</div>
                                <div class="irow_box_right">
                                    <input type="password" name="<?php echo iN_HelpSecure($sKey); ?>_cliend_secret" class="i_input flex_" value="<?php echo iN_HelpSecure($sKeyTwo); ?>" placeholder="Client Secret from Google Console">
                                    <?php if ($sKey === 'google'): ?>
                                        <div class="rec_not box_not_padding_left">
                                            <small>Client Secret from the same OAuth 2.0 Client ID</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="i_general_row_box_item flex_ tabing_non_justify">
                                <div class="irow_box_left tabing flex_"><?php echo iN_HelpSecure($LANG[$sKey]); ?> ICON ID</div>
                                <div class="irow_box_right">
                                    <input type="text" name="<?php echo iN_HelpSecure($sKey); ?>_icon" class="i_input flex_" value="<?php echo iN_HelpSecure($sLoginIcon); ?>">
                                    <div class="rec_not box_not_padding_left">
                                        <small>Icon ID for the login button (optional)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="i_general_row_box_item flex_ tabing__justify">
                                <div class="irow_box_left tabing flex_"><?php echo iN_HelpSecure($LANG[$sKey]); ?> Status</div>
                                <div class="irow_box_right">
                                    <div class="i_checkbox_wrapper flex_ tabing_non_justify">
                                        <label class="el-switch el-switch-yellow" for="<?php echo iN_HelpSecure($sKey); ?>_status">
                                            <input type="checkbox" class="slog" data-type="<?php echo iN_HelpSecure($sKey); ?>" id="<?php echo iN_HelpSecure($sKey); ?>_status" <?php echo iN_HelpSecure($sStatus) == '1' ? 'value="1" checked="checked"' : 'value="0"'; ?>>
                                            <span class="el-switch-style"></span>
                                        </label>
                                        <input type="hidden" name="<?php echo iN_HelpSecure($sKey); ?>_status" id="<?php echo iN_HelpSecure($sKey); ?>_statuss" <?php echo iN_HelpSecure($sStatus) == '1' ? 'value="1" checked="checked"' : 'value="0"'; ?>>
                                        <div class="success_tick tabing flex_ sec_one <?php echo iN_HelpSecure($sKey); ?>_status"><?php echo html_entity_decode($iN->iN_SelectedMenuIcon('69')); ?></div>
                                    </div>
                                    <div class="rec_not box_not_padding_left">
                                        <?php echo preg_replace('/{.*?}/', $sKey, $LANG['social_login_status_not']); ?>
                                        <?php if ($sKey === 'google' && (empty($sKeyOne) || empty($sKeyTwo))): ?>
                                            <br><strong style="color: red;">Warning: Enable only after configuring Client ID and Secret</strong>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ($sKey === 'google'): ?>
                                <div class="config-status" style="margin-top: 15px; padding: 10px; border-radius: 3px; <?php echo (empty($sKeyOne) || empty($sKeyTwo)) ? 'background-color: #fff3cd; border: 1px solid #ffeaa7;' : 'background-color: #d4edda; border: 1px solid #c3e6cb;'; ?>">
                                    <?php if (empty($sKeyOne) || empty($sKeyTwo)): ?>
                                        <strong>⚠ Configuration Incomplete</strong><br>
                                        Please configure both Client ID and Client Secret before enabling Google login.
                                    <?php else: ?>
                                        <strong>✓ Configuration Complete</strong><br>
                                        Google login is properly configured. Make sure the redirect URI is added to Google Console.
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                <?php
                    }
                }
                ?>
                
                <div class="i_settings_wrapper_item successNot"><?php echo iN_HelpSecure($LANG['updated_successfully']); ?></div>
                
                <div class="i_general_row_box_item flex_ tabing_non_justify">
                    <input type="hidden" name="f" value="sLoginSet">
                    <button type="submit" name="submit" class="i_nex_btn_btn transition" id="updateGeneralSettings"><?php echo iN_HelpSecure($LANG['save_edit']); ?></button>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background-color: #e3f2fd; border: 1px solid #bbdefb; border-radius: 5px;">
                    <h4>Troubleshooting</h4>
                    <p>If Google login is not working:</p>
                    <ul>
                        <li>Run the diagnostic tool: <a href="<?php echo iN_HelpSecure($base_url); ?>google_oauth_diagnostic.php" target="_blank">Google OAuth Diagnostic</a></li>
                        <li>Verify redirect URI in Google Console matches exactly: <code><?php echo iN_HelpSecure($base_url); ?>googleLogin.php</code></li>
                        <li>Ensure Google+ API or People API is enabled in Google Cloud Console</li>
                        <li>Check that OAuth consent screen is configured</li>
                    </ul>
                </div>
            </form>
        </div>
    </div>
</div>