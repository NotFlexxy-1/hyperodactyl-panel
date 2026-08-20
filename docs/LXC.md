# LXC Integration

Hyperodactyl can provision and manage LXC/LXD containers alongside game servers.
This is a real integration: every action issues an API call to an actual
hypervisor host. No functionality is simulated.

## Supported backends

| Driver    | Endpoint            | Auth                                   |
|-----------|---------------------|----------------------------------------|
| `lxd`     | LXD REST API (8443) | client certificate + key (PEM)         |
| `proxmox` | Proxmox VE API      | API token (`user@realm!tokenid=secret`)|

## Node requirements

- Debian 12 / Ubuntu 22.04+ host with `lxd` (>= 5.x) or Proxmox VE 8.
- LXD: `lxc config set core.https_address :8443` and a trusted client cert.
- Proxmox: an API token with `VM.Allocate`, `VM.Config.*`, `VM.Console`,
  `VM.PowerMgmt` and `Datastore.AllocateSpace` on the target pool.
- A storage pool and a bridge (e.g. `vmbr0` / `lxdbr0`) reachable from the panel.
- Outbound HTTPS from the panel to the node API port.

## Panel configuration

1. Admin -> LXC -> Nodes -> Create: set driver, API URL, credentials, storage
   pool, bridge and resource capacity.
2. Admin -> LXC -> Containers: create a container for a user. Provisioning is
   dispatched to `ProvisionContainerJob` on the queue.
3. Ensure a queue worker is running: `php artisan queue:work --queue=standard`.

## Lifecycle

- Create -> `ProvisionContainerJob` -> driver `create()` + `start()`.
- Power actions (start/stop/restart/kill) hit the driver directly.
- Resource changes (CPU, memory, disk) are applied live where the driver
  supports hot-resize, otherwise on next boot.
- Snapshots are native LXD/Proxmox snapshots; restore reverts the volume.
- Console uses the driver's websocket (LXD `exec`, Proxmox `termproxy`).

## Permissions

Container access is granted through the owning user plus the `lxc.*`
permission set; root admins bypass the check. The client API is exposed at
`/api/client/lxc/*` and the admin/application API at `/api/application/lxc/*`.

## Troubleshooting

- `driver_unreachable`: verify the node URL, certificate/token and firewall.
- Containers stuck `installing`: the queue worker is not running.
- Console fails to attach: the node must be reachable over websocket from the
  browser; terminate TLS with a valid certificate.
