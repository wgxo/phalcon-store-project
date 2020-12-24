<?php
declare(strict_types=1);

class IndexController extends ControllerBase {

    public function indexAction(): void {
        $this->view->setVar('polls', Polls::find([
            'cache' => [
                'key'      => 'polls',
                'lifetime' => 300,
            ],
        ]));

        $this->session->set('userName', 'wgarcia');
    }

    /**
     * @throws ImagickException
     */
    public function logoAction(): void {
    	// no layout
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);

        // use chache
	    $key = 'polls-logo2';

	    if (!apcu_exists($key)) {

		    /* Read the image */
		    $im = new Imagick(__DIR__ . "/../../public/img/polls.png");

		    /* Thumbnail the image */
		    $im->thumbnailImage(400, 0);

		    /* Create a border for the image */
		    // $im->borderImage(new ImagickPixel("white"), 5, 5);

		    /* Clone the image and flip it on the horizontal axis */
		    $reflection = clone $im;
		    $reflection->flipImage();

		    /* Create gradient. It will be overlayed on the reflection */
		    $gradient = new Imagick();

		    /* Gradient needs to be large enough for the image and the borders */
		    $gradient->newPseudoImage($reflection->getImageWidth(), $reflection->getImageHeight(), "gradient:transparent-white");

		    /* Composite the gradient on the reflection */
		    $reflection->compositeImage($gradient, imagick::COMPOSITE_OVER, 0, 0);

		    /* Add some opacity. Requires ImageMagick 6.2.9 or later */
		    $reflection->setImageAlpha(0.3);

		    /* Create an empty canvas */
		    $canvas = new Imagick();

		    /* Canvas needs to be large enough to hold the both images */
		    $width = $im->getImageWidth() + 40;
		    $height = ($im->getImageHeight() * 2) + 30;
		    $canvas->newImage($width, $height, new ImagickPixel("white"));
		    $canvas->setImageFormat("png");

		    /* Composite the original image and the reflection on the canvas */
		    $canvas->compositeImage($im, imagick::COMPOSITE_OVER, 20, 10);
		    $canvas->compositeImage($reflection, imagick::COMPOSITE_OVER, 20, $im->getImageHeight());

		    ob_start();
		    echo $canvas;
		    $img = ob_get_contents();
		    apcu_add($key, base64_encode($img));
		    ob_end_clean();
	    } else {
	    	$b64 = apcu_fetch($key);
	    	$img = base64_decode($b64);
	    }

	    /* Output the image*/
	    header("Content-Type: image/png");
	    echo $img;
    }
}
