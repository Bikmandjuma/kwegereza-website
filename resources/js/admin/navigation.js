/**
 * Single source of truth for the sidebar's grouped nav, also reused by
 * the Dashboard's quick-links grid so the two never drift out of sync
 * with each other.
 */
export const NAV_GROUPS = [
  {
    label: 'Ubwigishe',
    items: [
      { to: '/courses', label: 'Amasomo', permission: 'courses.view' },
      { to: '/darsat', label: 'Darsat', permission: 'darsat.view' },
      { to: '/quizzes', label: 'Ibizamini', permission: 'quizzes.view' },
    ],
  },
  {
    label: 'Ibinyamakuru',
    items: [
      { to: '/books', label: 'Ibitabo', permission: 'books.view' },
      { to: '/amatangazo', label: 'Amatangazo', permission: 'amatangazo.view' },
    ],
  },
  {
    label: 'Itumanaho',
    items: [
      { to: '/chat', label: 'Ubutumwa', permission: 'chat.view' },
      { to: '/group-chat/leaders', label: "Itsinda ry'Abayobozi", permission: 'group_chat.leaders' },
      { to: '/comment-moderation', label: 'Ibitekerezo', permission: 'comments.moderate' },
    ],
  },
  {
    label: 'Abanyeshuri',
    items: [
      { to: '/students', label: 'Abanyeshuri', permission: 'students.view' },
      { to: '/certificates', label: 'Ibyemezo', permission: 'certificates.view' },
      { to: '/badges', label: 'Ibimenyetso', permission: 'gamification.view' },
    ],
  },
  {
    label: 'Ibikorwa',
    items: [
      { to: '/events', label: 'Ibikorwa', permission: 'events.view' },
      { to: '/live-classes', label: 'Amasomo ya Live', permission: 'live_class.manage' },
    ],
  },
  {
    label: 'Ibipimo',
    items: [
      { to: '/analytics', label: 'Ibipimo', permission: 'analytics.view' },
      { to: '/audit-logs', label: 'Ibikorwa (Audit Log)', permission: 'audit_logs.view' },
      { to: '/system-monitor', label: 'System Monitor', permission: 'system_monitoring.view' },
    ],
  },
  {
    label: 'Ubuyobozi',
    items: [
      { to: '/staff', label: 'Abakoresha', permission: 'users.view' },
      { to: '/roles', label: 'Amashimikiro', permission: 'roles.view' },
      { to: '/teacher-verification', label: 'Kwemeza Abarimu', permission: 'teacher_verification.manage' },
    ],
  },
  {
    label: 'Sisitemu',
    items: [
      { to: '/feature-flags', label: 'Feature Flags', permission: 'feature_flags.manage' },
      { to: '/backups', label: 'Backups', permission: 'backups.view' },
      { to: '/account-deletions', label: 'Gusiba Konti', permission: 'account_deletion.manage' },
    ],
  },
]
