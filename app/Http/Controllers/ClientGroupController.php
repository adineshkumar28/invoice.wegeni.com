<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientGroupRequest;
use App\Http\Requests\UpdateClientGroupRequest;
use App\Models\ClientGroup;
use App\Repositories\ClientGroupRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laracasts\Flash\Flash;

class ClientGroupController extends AppBaseController
{
    /**
     * @var ClientGroupRepository
     */
    private $clientGroupRepository;

    public function __construct(ClientGroupRepository $clientGroupRepo)
    {
        $this->clientGroupRepository = $clientGroupRepo;
    }

    /**
     * @throws Exception
     */
    public function index(): Factory|View|Application
    {
        return view('client-groups.index');
    }

    public function create(): View|Factory|Application
    {
        return view('client-groups.create');
    }

    public function store(CreateClientGroupRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['tenant_id'] = getLogInUser()->tenant_id;
        
        $this->clientGroupRepository->create($input);
        Flash::success(__('messages.flash.client_group_created'));

        return redirect()->route('client-groups.index');
    }

    public function show($clientGroupId): View|Factory|Application
    {
        $clientGroup = ClientGroup::with('clients.user')->findOrFail($clientGroupId);
        
        return view('client-groups.show', compact('clientGroup'));
    }

    public function edit($clientGroupId): View|Factory|Application
    {
        $clientGroup = ClientGroup::findOrFail($clientGroupId);
        
        return view('client-groups.edit', compact('clientGroup'));
    }

    public function update($clientGroupId, UpdateClientGroupRequest $request): RedirectResponse
    {
        $clientGroup = ClientGroup::findOrFail($clientGroupId);
        $input = $request->all();
        
        $this->clientGroupRepository->update($input, $clientGroupId);
        Flash::success(__('messages.flash.client_group_updated'));

        return redirect()->route('client-groups.index');
    }

    public function destroy($clientGroupId): JsonResponse
    {
        $clientGroup = ClientGroup::findOrFail($clientGroupId);
        
        // Check if group has clients
        if ($clientGroup->clients()->count() > 0) {
            return $this->sendError(__('messages.flash.client_group_has_clients'));
        }
        
        $clientGroup->delete();
        
        return $this->sendSuccess(__('messages.flash.client_group_deleted'));
    }
}
