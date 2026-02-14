# Staff Table Migration - Updated Plan Summary

**Last Updated:** Feb 2026  
**Status:** Plan updated based on user answers

---

## Key Decisions Made

### ✅ Database Analysis Results
- **Staff:** 96 records, ID range 1-45515
- **Clients:** 9,345 records, ID range 2-45956
- **⚠️ ID OVERLAP DETECTED** → Must use **Option B** (new IDs + mapping table)

### ✅ User Requirements Clarified

| Question | Answer | Impact |
|----------|--------|--------|
| Migration approach | One phase at a time | Proceed incrementally |
| Staff vs Client login | Only staff use CRM; clients use mobile app API only | No session collision; sessions table is staff-only |
| Company contact person | Clients only, not staff | Keep `companies.contact_person_id` on `admins` |
| Device/refresh tokens | Clients only (mobile app) | No migration needed; stay on `admins` |
| Messages/notifications | Staff ↔ Client via API | Polymorphic: add `sender_type`/`recipient_type` columns |

---

## Major Plan Changes

1. **ID Strategy: Option B Required**
   - Create `admin_staff_id_mapping` table
   - All 30+ FK migrations must use mapping table
   - More complex but necessary due to ID overlap

2. **Session Table: Simplified**
   - ✅ Sessions are staff-only (no clients)
   - ✅ No collision risk
   - Update `sessions.user_id` using mapping table

3. **Tokens: No Migration**
   - ✅ `device_tokens` and `refresh_tokens` are client-only
   - ✅ No changes needed

4. **Auth Guards: Clear Separation**
   - `admin` + `web` guards → Staff (CRM login)
   - `api` guard → Clients (mobile app)
   - No overlap, clean separation

5. **Risks Reduced**
   - ~~Session collision~~ → RESOLVED (staff-only)
   - ~~Token polymorphic complexity~~ → RESOLVED (client-only)
   - ~~Company contact person ambiguity~~ → RESOLVED (client-only)

---

## Next Steps & Remaining Questions

### Immediate Actions

1. **Choose Migration Approach:**
   - **Option A: Full Migration** (all phases at once)
     - Pros: Done in one release
     - Cons: Higher risk, longer maintenance window
     - Timeline: 2-3 weeks dev + 1 week QA + 4-hour deployment
   
   - **Option B: Incremental Migration** (recommended)
     - Phase 0: Add `is_staff` boolean, use scopes (1 week)
     - Phase 1: Create staff table, copy data, dual-write (1 week)
     - Phase 2: Switch auth, migrate FKs (2 weeks)
     - Pros: Lower risk per release, easier rollback
     - Cons: Takes 4-6 weeks total

   **❓ QUESTION 1: Which approach do you prefer?**

2. **Timeline & Resources:**
   - When do you want to start?
   - How many developers available?
   - Do you have a staging environment that mirrors production?
   - Can you schedule a 4-hour maintenance window?

   **❓ QUESTION 2: What's your timeline and team size?**

3. **Testing Strategy:**
   - Do you have automated tests?
   - Can you allocate 1 week for QA?
   - Who will perform the testing?

   **❓ QUESTION 3: How will testing be done?**

---

## Updated Risk Assessment

| Risk Level | Count | Status |
|------------|-------|--------|
| Critical | 1 | Client Portal API (test thoroughly) |
| High | 3 | FK migrations, password reset, lockout |
| Medium | 4 | Polymorphic refs, incomplete FKs, rollback, old code |
| Low | 2 | Performance, data loss |
| **RESOLVED** | 1 | ✅ Session collision (staff-only) |

**Top 3 Priorities:**
1. FK migration with mapping table (30+ tables)
2. Staff lockout prevention (staging test + rollback)
3. Client Portal API unchanged (no impact to mobile app)

---

## Migration Phases Summary

| Phase | Task | Complexity | Risk |
|-------|------|------------|------|
| 1 | Create staff table + model | Low | Low |
| 2 | Auth config + guards | Medium | High |
| 3 | Copy staff data + create mapping | Medium | Medium |
| 4 | Migrate 30+ FK columns | High | High |
| 5 | Update models + controllers | High | Medium |
| 6 | Remove staff from admins (defer) | Low | Low |
| 7 | Client Portal (no changes) | Low | Low |

**Critical path:** Phases 3-5 must be atomic (single deployment).

---

## Files Requiring Changes

| Category | Count | Notes |
|----------|-------|-------|
| Migrations | 6-8 | create_staff, copy_data, mapping, FKs, polymorphic |
| Models | 30+ | BelongsTo Staff or polymorphic |
| Controllers | 15+ | Use Staff instead of Admin for staff ops |
| Services | 2 | ActiveUserService, possibly others |
| Views | 20+ | Staff dropdowns, AdminConsole |
| Config | 1 | auth.php |

**Total estimated:** 70-100 files

---

## Deployment Checklist (High-Level)

### 1 Week Before
- [ ] All code complete
- [ ] Staging migration successful
- [ ] All tests pass
- [ ] Rollback tested
- [ ] Staff notification sent

### Deployment Day
- [ ] DB backup
- [ ] Code deploy
- [ ] Run migrations (create, copy, map, FKs)
- [ ] Flush sessions
- [ ] Update auth config
- [ ] Smoke tests
- [ ] Monitor 4 hours

### 48 Hours After
- [ ] Monitor login rates
- [ ] Check FK violations
- [ ] Verify no session bleed
- [ ] Spot-check features

---

## Ready to Proceed?

The plan is now updated with your requirements. Before moving forward, please answer:

1. **Which migration approach:** Full (one release) or Incremental (3 releases)?
2. **Timeline:** When can you start? How long can each phase take?
3. **Resources:** How many developers? Do you have staging?
4. **Testing:** Automated tests available? QA resource allocated?
5. **Approval:** Do you have stakeholder approval for a 4-hour maintenance window?

Once you answer these, I can create:
- Detailed migration scripts for each phase
- Testing checklists
- Rollback procedures
- Developer task assignments

**What would you like me to focus on next?**
