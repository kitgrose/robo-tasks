# robo-tasks
Custom tasks for Robo Task Runner

## Docker

Extensions to the built-in Robo Docker tasks

### Docker\Port

Helpers for `docker port`. Lists port mappings or a specific mapping for a
container.

`externalPort($internalPortNumber)` returns the external port mapped to the
specified port. Input can be either an integer port number or a string in the
form port/protocol (e.g. `80/tcp`).

### Docker\Inspect

Helpers for `docker inspect`.

`isRunning()` returns true if the container is running, false if it is stopped
and throws an `\Exception` if the specified container name is not known to
Docker.