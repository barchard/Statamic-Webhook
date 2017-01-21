<?php

namespace Statamic\Addons\Webhook;

use Statamic\Extend\Controller;

class WebhookController extends Controller
{
    /**
     * Maps to your route definition in routes.yaml
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return $this->view('index');
    }

    /**
     *
     */
    public function postGithub()
    {
        $signed_request = Request::header('X-Hub-Signature');
        $content = $request->getContent();
        $github = $this->getConfig('github', []);
        if (!isset($github['secret_token']) ||
            empty($github['secret_token']) ||
            !$this->verifySignature($content, $signed_request, $github['secret_token'])) {
            // Abort with a 404 error. We don't want to leak the existence
            // of the URL or the reason for failure
            abort(404);
        }

        // Process each of the commands serially.
        // If *any* returns a non-zero exit code, abort
        $commands = isset($github['commands']) && is_array($github['commands']) ?
            $github['commands'] : [];
        foreach ($commands as $command) {
            
        }
    }

    /**
     * Is the current environment whitelisted?
     *
     * @return bool
     */
    private function environmentWhitelisted()
    {
        return in_array(app()->environment(), $this->getConfig('environments', []));
    }

    /**
     * Verify the content is valid
     * @link https://developer.github.com/webhooks/
     * @param $content The content of the request
     * @param $signed_request The signed signature of the request
     * @param $secret The shared secret
     * @return bool
     */
    private function verifySignature($content, $signed_request, $shared_secret)
    {
        $hashed_content = base64_encode(hash_hmac('sha1', $content, $shared_secret, true));
        return $this->hash_compare($hashed_content, $signed_request);
    }

    /**
     * Securely compare two hashes. Do *not* use == to prevent timing based attacks!
     * @link http://php.net/manual/en/function.hash-hmac.php#111435
     * @return bool
     */
    private function hash_compare($a, $b)
    {
        if (!is_string($a) || !is_string($b)) {
            return false;
        }

        $len = strlen($a);
        if ($len !== strlen($b)) {
            return false;
        }

        $status = 0;
        for ($i = 0; $i < $len; $i++) {
            $status |= ord($a[$i]) ^ ord($b[$i]);
        }

        return $status === 0;
    }
}
