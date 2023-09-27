<?php

declare(strict_types=1);

namespace ItalyStrap\Tests\Unit\Components;

use ItalyStrap\Components\ComponentInterface;
use ItalyStrap\Components\FeaturedImage;
use ItalyStrap\Tests\UnitTestCase;
use ItalyStrap\Theme\Infrastructure\Config\ConfigPostThumbnailProvider;
use PHPUnit\Framework\Assert;

class FeaturedImageTest extends UnitTestCase
{
    protected function getInstance(): FeaturedImage
    {
        $sut = new FeaturedImage($this->makeConfig(), $this->makeView());
        $this->assertInstanceOf(ComponentInterface::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldLoad()
    {
        $sut = $this->getInstance();

        $this->defineFunction('get_post_type', static fn() => 'post');

        $this->defineFunction(
            'post_type_supports',
            static function (string $post_type, string $feature) {
                Assert::assertEquals('post', $post_type, '');
                return true;
            }
        );

        $this->config->get('post_content_template', [])->willReturn([]);

        $this->assertTrue($sut->shouldDisplay(), '');
    }

    /**
     * @test
     */
    public function itShouldDisplay()
    {
        $sut = $this->getInstance();
        $this->defineFunction('is_singular', fn() => false);

        $size_slig_result = 'full_width';
        $this->config->get(ConfigPostThumbnailProvider::POST_THUMBNAIL_SIZE)->willReturn($size_slig_result);
        $this->config->get('site_layout')->willReturn($size_slig_result);
        $this->config->get(ConfigPostThumbnailProvider::POST_THUMBNAIL_ALIGNMENT)->willReturn($size_slig_result);

//      $this->defineFunction('is_page_template', static function ( string $template ) {
//          Assert::assertSame('full-width.php', $template, '');
//          return 'full-width';
//      });

        $this->defineFunction('do_blocks', static fn(string $block) => 'From do_blocks');

        $this->expectOutputString('From do_blocks');
        $sut->display();
    }
}
