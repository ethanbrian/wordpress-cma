<?php

/**
 * Payment Pending Authorization - Email.
 * 
 * @class SUMOSubscriptions_Pending_Authorization_Email
 */
class SUMOSubscriptions_Pending_Authorization_Email extends SUMO_Abstract_Subscription_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id             = 'subscription_pending_authorization' ;
		$this->name           = 'pending-authorization' ;
		$this->customer_email = true ;
		$this->title          = __( 'Subscription Pending Authorization', 'sumosubscriptions' ) ;
		$this->description    = addslashes( __( 'Subscription Pending Authorization emails are sent to the customer when authorized card is declined by the bank.', 'sumosubscriptions' ) ) ;

		$this->template_html  = 'emails/subscription-pending-authorization.php' ;
		$this->template_plain = 'emails/plain/subscription-pending-authorization.php' ;

		$this->subject = __( '[{site_title}] - Subscription Pending Authorization', 'sumosubscriptions' ) ;
		$this->heading = __( 'Subscription Pending Authorization', 'sumosubscriptions' ) ;

		$this->supports = array( 'mail_to_admin', 'pay_link', 'upcoming_mail_info' ) ;

		// Call parent constuctor
		parent::__construct() ;
	}

}

return new SUMOSubscriptions_Pending_Authorization_Email() ;
