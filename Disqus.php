<?php

namespace w3lifer\yii2;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Widget;
use yii\web\View;

/**
 * Disqus widget.
 * @see https://disqus.com
 * @see https://disqus.com/admin/settings/universalcode
 * @see https://help.disqus.com/customer/portal/articles/466249-multi-lingual-websites
 */
class Disqus extends Widget
{
    /**
     * @var string
     */
    public $shortName;

    /**
     * @var string
     */
    public $pageUrl;

    /**
     * @var string
     */
    public $pageIdentifier;

    /**
     * @var string
     */
    public $language;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!$this->shortName) {
            throw new InvalidArgumentException(
                'You must specify the "shortName"'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $js = 'var disqus_config = function() {';
        if ($this->pageUrl) {
            $js .= 'this.page.url = "' . $this->pageUrl . '";';
        }
        if ($this->pageIdentifier) {
            $js .= 'this.page.identifier = "' . $this->pageIdentifier . '";';
        }
        $js .=
            'this.language = "' .
                ($this->language ? $this->language : Yii::$app->language)
            . '";';
        $js .= '};';

        $js .= <<<JS
(function() {
var d = document, s = d.createElement('script');
s.src = 'https://{$this->shortName}.disqus.com/embed.js';
s.setAttribute('data-timestamp', +new Date());
(d.head || d.body).appendChild(s);
})();
JS;

        $this->view->registerJsFile(
            'https://' . $this->shortName . '.disqus.com/count.js',
            [
                'async' => true,
                'id' => 'dsq-count-scr',
            ]
        );
        $this->view->registerJs($js, View::POS_END);

        return '<div id="disqus_thread"></div>';
    }
}