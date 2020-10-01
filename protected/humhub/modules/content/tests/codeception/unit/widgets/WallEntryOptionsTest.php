<?php


namespace humhub\modules\content\tests\codeception\unit\widgets;


use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\content\widgets\stream\StreamEntryOptions;
use humhub\modules\content\widgets\stream\StreamEntryWidget;
use humhub\modules\content\widgets\stream\WallStreamEntryOptions;
use humhub\modules\content\widgets\WallEntryControls;
use humhub\modules\post\models\Post;
use humhub\modules\post\widgets\WallEntry;
use tests\codeception\_support\HumHubDbTestCase;

class WallEntryOptionsTest extends HumHubDbTestCase
{
    public function testConstructorWithBaseOptions()
    {
        $streamOptions = (new StreamEntryOptions)->viewContext('test')
            ->overwriteWidgetClass(WallEntry::class);

        $wallStreamEntryOptions = new StreamEntryOptions($streamOptions);
        $this->assertTrue($wallStreamEntryOptions->isViewContext('test'));
        $this->assertEquals(WallEntry::class, $wallStreamEntryOptions->getStreamEntryWidgetClass());
    }

    public function testDisableControlsEdit()
    {
        $this->testDisableControlsItem('Edit',
            (new WallStreamEntryOptions)->disableControlsEntryEdit());
    }

    public function testDisableControlsDelete()
    {
        $this->testDisableControlsItem('Delete',
            (new WallStreamEntryOptions)->disableControlsEntryDelete());
    }

    public function testDisableControlsTopic()
    {
        $this->testDisableControlsItem('Topics',
            (new WallStreamEntryOptions)->disableControlsEntryTopics());
    }

    public function testDisableControlsPermalink()
    {
        $this->testDisableControlsItem('Permalink',
            (new WallStreamEntryOptions)->disableControlsEntryPermalink());
    }

    public function testDisableControlsSwitchVisibility()
    {
        $this->testDisableControlsItem('Make public',
            (new WallStreamEntryOptions)->disableControlsEntrySwitchVisibility());
    }

    public function testDisableControlsSwitchNotification()
    {
        $this->testDisableControlsItem('Turn on notifications',
            (new WallStreamEntryOptions)->disableControlsEntrySwitchNotification());
    }

    public function testDisableControlsPin()
    {
        $this->testDisableControlsItem('Pin to top',
            (new WallStreamEntryOptions)->disableControlsEntryPin());
    }

    public function testDisableControlsMove()
    {
        $this->testDisableControlsItem('Move',
            (new WallStreamEntryOptions)->disableControlsEntryMove());
    }

    public function testDisableControlsArchive()
    {
        $this->testDisableControlsItem('Move to archive',
            (new WallStreamEntryOptions)->disableControlsEntryArchive());
    }

    public function testDisableControlsMenu()
    {
        $this->assertWallEntryControlsNotContains('preferences',  (new WallStreamEntryOptions)->disableControlsMenu());
    }

    public function testDisableAddonsMenu()
    {
        $this->assertWallEntryContains('stream-entry-addons',  new WallStreamEntryOptions);
        $this->assertWallEntryNotContains('stream-entry-addons', (new WallStreamEntryOptions)->disableAddons());
    }

    public function testDisableCommentAddonsMenu()
    {
        $this->assertWallEntryContains('comment-container',  new WallStreamEntryOptions);
        $this->assertWallEntryNotContains('comment-container', (new WallStreamEntryOptions)->disableCommentAddon());
    }

    public function testDisableWallEntryLinksAddonsMenu()
    {
        $this->assertWallEntryContains('wall-entry-links',  new WallStreamEntryOptions);
        $this->assertWallEntryNotContains('wall-entry-links', (new WallStreamEntryOptions)->disableWallEntryLinks());
    }

    public function testSearchStreamDoesOnlyIncludePermalink()
    {
        $this->assertWallEntryControlsContains('Permalink',  (new WallStreamEntryOptions)->viewContext(WallStreamEntryOptions::VIEW_CONTEXT_SEARCH));
        $this->assertWallEntryControlsNotContains('Delete',  (new WallStreamEntryOptions)->viewContext(WallStreamEntryOptions::VIEW_CONTEXT_SEARCH));
        $this->assertWallEntryControlsNotContains('Edit',  (new WallStreamEntryOptions)->viewContext(WallStreamEntryOptions::VIEW_CONTEXT_SEARCH));
        $this->assertWallEntryControlsNotContains('Move to archive',  (new WallStreamEntryOptions)->viewContext(WallStreamEntryOptions::VIEW_CONTEXT_SEARCH));
        $this->assertWallEntryControlsNotContains('Move',  (new WallStreamEntryOptions)->viewContext(WallStreamEntryOptions::VIEW_CONTEXT_SEARCH));
        $this->assertWallEntryControlsNotContains('Pin to top',  (new WallStreamEntryOptions)->viewContext(WallStreamEntryOptions::VIEW_CONTEXT_SEARCH));
        $this->assertWallEntryControlsNotContains('Turn on notifications',  (new WallStreamEntryOptions)->viewContext(WallStreamEntryOptions::VIEW_CONTEXT_SEARCH));
        $this->assertWallEntryControlsNotContains('Make public',  (new WallStreamEntryOptions)->viewContext(WallStreamEntryOptions::VIEW_CONTEXT_SEARCH));
    }

    private function testDisableControlsItem($searchStr, $renderOptions)
    {
        $this->becomeUser('admin');

        $model = Post::findOne(['id' => 1]);

        // In order for pin controls to work
        ContentContainerHelper::setCurrent($model->content->container);

        $wallEntry = new WallEntry(['model' => $model]);
        $result = WallEntryControls::widget(['object' => $model, 'wallEntryWidget' => $wallEntry]);
        $this->assertContains($searchStr, $result);

        $wallEntry = new WallEntry(['model' => $model, 'renderOptions' => $renderOptions]);

        $this->assertNotContains($searchStr, WallEntryControls::widget([
            'object' => $model,
            'wallEntryWidget' => $wallEntry
        ]));
    }

    private function assertWallEntryControlsContains($searchStr, $renderOptions)
    {
        $this->becomeUser('admin');

        $model = Post::findOne(['id' => 1]);

        // In order for pin controls to work
        ContentContainerHelper::setCurrent($model->content->container);

        $wallEntry = new WallEntry(['model' => $model, 'renderOptions' => $renderOptions]);

        $this->assertContains($searchStr, WallEntryControls::widget([
            'object' => $model,
            'wallEntryWidget' => $wallEntry
        ]));
    }

    private function assertWallEntryControlsNotContains($searchStr, $renderOptions)
    {
        $this->becomeUser('admin');

        $model = Post::findOne(['id' => 1]);

        // In order for pin controls to work
        ContentContainerHelper::setCurrent($model->content->container);

        $wallEntry = new WallEntry(['model' => $model, 'renderOptions' => $renderOptions]);

        $this->assertNotContains($searchStr, WallEntryControls::widget([
            'object' => $model,
            'wallEntryWidget' => $wallEntry
        ]));
    }

    private function assertWallEntryNotContains($searchStr, $renderOptions)
    {
        $this->becomeUser('admin');

        $model = Post::findOne(['id' => 1]);

        // In order for pin controls to work
        ContentContainerHelper::setCurrent($model->content->container);

        $this->assertNotContains($searchStr, StreamEntryWidget::renderStreamEntry($model, $renderOptions));
    }

    private function assertWallEntryContains($searchStr, $renderOptions)
    {
        $this->becomeUser('admin');

        $model = Post::findOne(['id' => 1]);

        // In order for pin controls to work
        ContentContainerHelper::setCurrent($model->content->container);

        $this->assertContains($searchStr, StreamEntryWidget::renderStreamEntry($model, $renderOptions));
    }
}
