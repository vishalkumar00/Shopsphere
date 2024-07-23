<?php 
require '../vendor/autoload.php';

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;

// Set up your PayPal credentials
$clientId = 'AfFnIfADz3MFxoYQCtrbGSRh57UBZnh68215Bx__mZNKN63dhapnnlEKkPK4hbWFSawNS8oKxZmneZ0f';
$clientSecret = 'EJwJotMmhZ7f9PYnUlmqyemCc_Mg81kYl4jELQTHl-Ob73rfIUuC07Ovu3NuoLUphEt9_tfLBPMPCmrd';

$environment = new SandboxEnvironment($clientId, $clientSecret);
$client = new PayPalHttpClient($environment);
?>