<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use App\Services\ProjectPricingService;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    public function print(Request $request, Project $project)
    {
        $customer = $project->customer;
        
        // Carregar relacionamentos necessários
        $project->load([
            'vms.disks.diskType',
            'vms.osDistribution',
            'vms.endpointSecurity',
            'vms.backupRetention',
            's3Storage',
            'backupStandalone.backupSoftware',
            'backupStandalone.retention',
            'networkType',
            'bandwidthOption',
            'firewallOption',
            'lanType',
            'partner',
        ]);

        $pricingService = new ProjectPricingService();
        $pricingData = $pricingService->getDetailedPricing($project);

        return view('proposals.print', compact('project', 'customer', 'pricingData'));
    }
}
