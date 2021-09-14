<div class="wrap about-wrap mkdf-welcome-page">
    <div class="mkdf-welcome-page-heading">
        <div class="mkdf-welcome-page-logo">
            <img src="<?php echo esc_url( MIKADO_FRAMEWORK_MODULES_ROOT . '/welcome/assets/img/logo.png' ); ?>" alt="<?php esc_attr_e( 'Qode Logo', 'libero' ); ?>"/>
        </div>
        <h1 class="mkdf-welcome-page-title">
			<?php echo sprintf( esc_html__( 'Welcome to %s', 'libero' ), $theme_name ); ?>
            <small><?php echo esc_html( $theme_version ) ?></small>
        </h1>
    </div>
    <div class="mkdf-welcome-page-text">
		<?php echo sprintf( esc_html__( 'Thank you for installing %s - %s! Everything in %s is streamlined to make your website building experience as simple and fun as possible. We hope you love using it to make a spectacular website.', 'libero' ),
			$theme_name,
			$theme_description,
			$theme_name
		); ?>
    </div>
    <div class="mkdf-welcome-page-content">
        <div class="mkdf-welcome-page-screenshot">
            <img src="<?php echo esc_url( $theme_screenshot ); ?>" alt="<?php esc_attr_e( 'Theme Screenshot', 'libero' ); ?>"/>
        </div>
        <div class="mkdf-welcome-page-links-holder">
            <div class="mkdf-welcome-page-install-core">
                <p><?php esc_html_e( 'Please install and activate required plugins in order to gain access to all the theme functionalities and features.', 'libero' ); ?></p>
                <a class="mkdf-welcome-page-install-button" href="<?php echo add_query_arg( array( 'page' => 'install-required-plugins&plugin_status=install' ), esc_url( admin_url( 'themes.php' ) ) ); ?>">
					<?php esc_html_e( 'Install Required Plugins', 'libero' ); ?>
                </a>
            </div>

            <h3><?php esc_html_e( 'Useful Links:', 'libero' ); ?></h3>
            <ul class="mkdf-welcome-page-links">
                <li>
                    <a href="https://helpcenter.qodeinteractive.com" target="_blank"><?php esc_html_e( 'Help Center', 'libero' ); ?></a>
                </li>
                <li>
	                <a href="<?php echo sprintf('http://libero.%s-themes.com/documentation/', MIKADO_PROFILE_SLUG ); ?>" target="_blank"><?php esc_html_e( 'Theme Documentation', 'libero' ); ?></a>
                </li>
                <li>
                    <a href="https://qodeinteractive.com/" target="_blank"><?php esc_html_e( 'All Our Themes', 'libero' ); ?></a>
                </li>
                <li>
                    <a href="<?php echo add_query_arg( array( 'page' => 'install-required-plugins&plugin_status=install' ), esc_url( admin_url( 'themes.php' ) ) ); ?>"><?php esc_html_e( 'Install Required Plugins', 'libero' ); ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>