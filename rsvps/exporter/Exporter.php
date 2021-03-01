<?php

class Exporter
{
    public function init(): void
    {
        add_action('export_filters', [$this, 'renderExportFilters']);
        add_action('export_wp', [$this, 'generateCSV']);
    }

    public function renderExportFilters(): void
    {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-datepicker-style',
            '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');

        require 'filters.php';
    }

    public function generateCSV($args): void
    {
        if ($args['content'] !== 'rsvp') {
            return;
        }

        $startDate = new DateTime(sanitize_text_field($_GET['rsvp-start-date']));
        $endDate   = new DateTime(sanitize_text_field($_GET['rsvp-end-date']));
        $fileName  = "rsvp_export_{$startDate->format('m-d-Y')}_{$endDate->format('m-d-Y')}.csv";

        ob_clean();
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Content-Type: text/csv; charset=' . get_option('blog_charset'), true);

        $events = tribe_get_events([
            'start_date'     => $startDate->format('Y-m-d H:i:s'),
            'end_date'       => $endDate->format('Y-m-d H:i:s'),
            'posts_per_page' => -1,
        ]);

        $getEvent = static function (int $eventId) use ($events): ?WP_Post {
            foreach ($events as $event) {
                if ($event->ID === $eventId) {
                    return $event;
                }
            }

            return null;
        };

        $rsvps = get_event_rsvps(wp_list_pluck($events, 'ID'));

        $fields = [
            'volunteer_email',
            'volunteer_user_id',
            'attended',
        ];

        echo implode(',', [
                'RSVP ID',
                'Volunteer First Name',
                'Volunteer Last Name',
                'Volunteer Email',
                'Volunteer User ID',
                'Attended',
                'RSVP Date',
                'Event ID',
                'Event Name',
                'Event Date',
            ]) . "\r\n";

        foreach ($rsvps as $rsvp) {
            $row = [
                $rsvp->ID,
            ];

            // Get data from meta
            $meta = get_post_meta($rsvp->ID);

            $name  = explode(',', $meta['volunteer_name'][0] ?? '');
            $row[] = trim($name[0] ?? '');
            $row[] = trim($name[1] ?? '');

            foreach ($fields as $metaKey) {
                $row[] = $meta[$metaKey][0] ?? '';
            }

            $rsvpDate = new DateTime($meta['rsvp_date'][0]);
            $row[] = $rsvpDate->format('Y-m-d');

            // Get data from event
            $event = $getEvent((int)$meta['event_id'][0]);
            if ($event !== null) {
                $row[] = $event->ID;
                $row[] = '"' . $event->post_title . '"';
                $row[] = $event->_EventStartDate;
            }

            echo implode(',', $row) . "\r\n";
        }

        exit();
    }
}

(new Exporter())->init();
