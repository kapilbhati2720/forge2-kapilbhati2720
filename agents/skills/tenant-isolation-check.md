# Tenant Isolation Verification Skill
# Used by OpenClaw to verify multi-tenant data isolation after implementing any feature

name: tenant-isolation-check
description: >
  Runs a standard cross-tenant probe after any new endpoint is added.
  Creates two orgs, seeds data in each, and confirms neither can access the other's data.

## Checklist

After implementing any new API endpoint:

1. Create org A + user A (admin)
2. Create org B + user B (admin)  
3. Create resource R owned by org A
4. Authenticate as user B
5. Attempt GET /api/{resource}/{R.id} → expect 404 (not 403, leak-safe)
6. Attempt PUT /api/{resource}/{R.id} → expect 404
7. Attempt DELETE /api/{resource}/{R.id} → expect 404

## PHPUnit Helper Pattern

```php
// Create two isolated tenants
[$orgA, $adminA] = $this->createOrgWithAdmin('org-a');
[$orgB, $adminB] = $this->createOrgWithAdmin('org-b');

// Seed data in org A
$resource = Resource::factory()->create(['organization_id' => $orgA->id]);

// Probe from org B — must return 404
$this->actingAs($adminB)
     ->getJson("/api/resource/{$resource->id}")
     ->assertStatus(404);
```

## Why 404 not 403?

Returning 403 leaks the existence of the resource. Returning 404 (via OrganizationScope
filtering it out of the query) is the correct multi-tenant behavior.
