<?php

namespace App\FrontModule;
use App\Model\Articles;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
    private $articles;
    
    public function actionDefault()
    {
        $string = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc hendrerit feugiat tortor in ultricies. Sed aliquet enim non mauris consectetur, eget dapibus turpis gravida. Praesent viverra mauris vel arcu tristique gravida. Etiam imperdiet fringilla consectetur. Phasellus cursus ante eget fermentum dignissim. Vestibulum et neque nec tellus lobortis luctus quis vitae lacus. Nullam semper placerat nunc, ut imperdiet mi faucibus id. Ut sit amet magna eget justo feugiat scelerisque ac pretium augue.

Phasellus id neque massa. Aenean venenatis magna a purus laoreet lobortis. Phasellus pulvinar nulla a lorem rutrum porta ut nec lacus. Duis magna neque, cursus eu malesuada a, ornare sit amet lacus. Morbi libero lectus, pretium quis lectus non, elementum elementum urna. Aliquam eu accumsan tellus. Praesent convallis arcu id turpis ultrices pellentesque. In laoreet ut neque quis condimentum.

In leo arcu, aliquam non magna non, vestibulum luctus lacus. Proin placerat, risus a laoreet facilisis, elit erat auctor metus, vitae auctor nulla turpis ac felis. Integer volutpat adipiscing sagittis. Nulla posuere neque ut tellus malesuada lacinia. Aliquam dictum ac tortor a blandit. Nulla accumsan sagittis lectus eu convallis. Aliquam mattis placerat nisl, at fermentum risus suscipit eget. Praesent purus nibh, porta eget enim vitae, vestibulum mollis arcu. Aliquam eget tempus nunc. Mauris vel metus congue, vulputate nunc et, viverra velit. Nam semper convallis facilisis';
        $j = 0;
        for ($i = 0; $i < strlen($string); $i++) {
            if (!in_array($string[$i], array(',', '.'))) {
                $string[$j] = $string[$i];
                $j++;
            }
        }
        $this->template->tags = explode(' ', $string);
    }
    
    public function injectArticles(Articles $articles)
    {
        $this->articles = $articles;
    }

}
