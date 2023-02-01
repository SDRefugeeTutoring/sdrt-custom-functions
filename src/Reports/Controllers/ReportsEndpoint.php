<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Reports\Controllers;

use Closure;
use DateTime;
use SDRT\CustomFunctions\Reports\DataTransferObjects\Session;
use SDRT\CustomFunctions\Reports\Repositories\ReportsRepository;
use WP_REST_Request;
use WP_REST_Response;

class ReportsEndpoint
{
    private const NAMESPACE = 'sdrt/v1';

    private ReportsRepository $reportsRepository;

    public function __construct(ReportsRepository $reportsRepository)
    {
        $this->reportsRepository = $reportsRepository;
    }

    public function register(): void
    {
        register_rest_route(self::NAMESPACE, '/reports/sessions', [
            'methods' => 'GET',
            'callback' => [$this, 'getSessions'],
            'permission_callback' => [$this, 'reportsPermission'],
            'args' => [
                'startDate' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'required' => true,
                ],
                'endDate' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'required' => true,
                ],
                'category' => [
                    'type' => 'string',
                    'required' => false,
                ],
            ]
        ]);
    }

    public function getSessions(WP_REST_Request $request): WP_REST_Response
    {
        $startDate = new DateTime($request->get_param('startDate'));
        $endDate = new DateTime($request->get_param('endDate'));
        $category = $request->get_param('category');

        $sessions = $this->reportsRepository->getSessions($startDate, $endDate, $category);

        $filename = "sessions_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}.csv";

        $this->outputCSV(
            $filename,
            $sessions,
            ['Session ID', 'Name', 'Category', 'Total Attending', 'Total Attended', 'Start Date', 'End Date'],
            static function (Session $session) {
                return $session->toArray();
            }
        );
    }

    public function reportsPermission(): bool
    {
        return current_user_can('manage_options');
    }

    /**
     * @return never-return
     */
    private function outputCSV(string $fileName, array $data, array $headers, Closure $callback)
    {
        header("Access-Control-Expose-Headers: Content-Disposition", false);
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename=\"$fileName\"");

        $outputBuffer = fopen("php://output", 'wb');
        fputcsv($outputBuffer, $headers);
        foreach ($data as $val) {
            fputcsv($outputBuffer, $callback($val));
        }
        fclose($outputBuffer);

        exit;
    }
}