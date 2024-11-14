<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use function KokoAnalytics\extract_pageview_data;
use function KokoAnalytics\extract_event_data;
use function KokoAnalytics\get_client_ip;
use function KokoAnalytics\percent_format_i18n;

final class FunctionsTest extends TestCase
{
    public function testExtractPageviewData(): void
    {
       // incomplete params
       $this->assertEquals(extract_pageview_data([]), []);
       $this->assertEquals(extract_pageview_data(['p' => '1']), []);
       $this->assertEquals(extract_pageview_data(['nv' => '2']), []);
       $this->assertEquals(extract_pageview_data(['up' => '3']), []);
       $this->assertEquals(extract_pageview_data(['p' => '1', 'nv' => '2']), []);
       $this->assertEquals(extract_pageview_data(['p' => '1', 'up' => '3']), []);
       $this->assertEquals(extract_pageview_data(['nv' => '2', 'up' => '3']), []);

       // complete but invalid
       $this->assertEquals(extract_pageview_data(['p' => '', 'nv' => '', 'up' => '']), []);
       $this->assertEquals(extract_pageview_data(['p' => 'x', 'nv' => '2', 'up' => '3']), []);
       $this->assertEquals(extract_pageview_data(['p' => '1', 'nv' => 'x', 'up' => '3']), []);
       $this->assertEquals(extract_pageview_data(['p' => '1', 'nv' => '2', 'up' => 'x']), []);
       $this->assertEquals(extract_pageview_data(['p' => '1', 'nv' => '2', 'up' => '3', 'r' => 'not an url']), []);

       // complete and valid
      $this->assertEquals(extract_pageview_data(['p' => '1', 'nv' => '2', 'up' => '3']), ['p', 1, 2, 3, '']);
      $this->assertEquals(extract_pageview_data(['p' => '1', 'nv' => '2', 'up' => '3', 'r' => '']), ['p', 1, 2, 3, '']);
      $this->assertEquals(extract_pageview_data(['p' => '1', 'nv' => '2', 'up' => '3', 'r' => 'https://www.kokoanalytics.com']), ['p', 1, 2, 3, 'https://www.kokoanalytics.com']);
    }

    public function testExtractEventData(): void
    {
       // incomplete
       $this->assertEquals(extract_event_data([]), []);
       $this->assertEquals(extract_event_data(['e' => 'Event']), []);
       $this->assertEquals(extract_event_data(['p' => 'Param']), []);
       $this->assertEquals(extract_event_data(['u' => '1']), []);
       $this->assertEquals(extract_event_data(['v' => '1']), []);
       $this->assertEquals(extract_event_data(['e' => 'Event', 'v' => '1']), []);
       $this->assertEquals(extract_event_data(['e' => 'Event', 'u' => '1']), []);
       $this->assertEquals(extract_event_data(['p' => 'Param', 'v' => '1']), []);
       $this->assertEquals(extract_event_data(['p' => 'Param', 'u' => '1']), []);
       $this->assertEquals(extract_event_data(['e' => 'Event', 'p' => 'Param', 'u' => '1']), []);
       $this->assertEquals(extract_event_data(['e' => 'Event', 'p' => 'Param', 'v' => '1']), []);

       // complete but invalid
       $this->assertEquals(extract_event_data(['e' => 'Event', 'p' => 'Param', 'u' => '1', 'v' => 'nan']), []);
       $this->assertEquals(extract_event_data(['e' => 'Event', 'p' => 'Param', 'u' => 'nan', 'v' => '100']), []);
       $this->assertEquals(extract_event_data(['e' => 'Event', 'p' => 'Param', 'u' => 'nan', 'v' => 'nan']), []);
       $this->assertEquals(extract_event_data(['e' => '', 'p' => 'Param', 'u' => '1', 'v' => '100']), []);

       // complete and valid
       $this->assertEquals(extract_event_data(['e' => 'Event', 'p' => 'Param', 'u' => '1', 'v' => '100']), ['e', 'Event', 'Param', 1, 100]);
    }

    public function testGetClientIp(): void
    {
        $this->assertEquals(get_client_ip(), '');

        $_SERVER['REMOTE_ADDR'] = '1.1.1.1';
        $this->assertEquals(get_client_ip(), '1.1.1.1');

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '2.2.2.2';
        $this->assertEquals(get_client_ip(), '2.2.2.2');

        $_SERVER['HTTP_X_FORWARDED_FOR'] = '3.3.3.3, 2.2.2.2';
        $this->assertEquals(get_client_ip(), '3.3.3.3');

        $_SERVER['HTTP_X_FORWARDED_FOR'] = 'not-an-ip';
        $this->assertEquals(get_client_ip(), '1.1.1.1');
    }

    public function testPercentFormatI18n(): void
    {
        $this->assertEquals(percent_format_i18n(0), '');
        $this->assertEquals(percent_format_i18n(0.00), '');
        $this->assertEquals(percent_format_i18n(1.00), '+100%');
        $this->assertEquals(percent_format_i18n(-1.00), '-100%');
        $this->assertEquals(percent_format_i18n(0.55), '+55%');
        $this->assertEquals(percent_format_i18n(-0.55), '-55%');

    }
}
