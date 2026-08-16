import client from '../../api/client'

// Certificates
export const listCertificates = (page = 1, search = '') => client.get('/certificates', { params: { page, search } }).then(r => r.data)
export const issueCertificate = (payload) => client.post('/certificates', payload).then(r => r.data)

// Badges (Gamification)
export const listBadges = () => client.get('/badges').then(r => r.data)
export const createBadge = (payload) => client.post('/badges', payload).then(r => r.data)
export const updateBadge = (id, payload) => client.put(`/badges/${id}`, payload).then(r => r.data)
export const deleteBadge = (id) => client.delete(`/badges/${id}`).then(r => r.data)

// Feature Flags
export const listFeatureFlags = () => client.get('/feature-flags').then(r => r.data)
export const toggleFeatureFlag = (id) => client.post(`/feature-flags/${id}/toggle`).then(r => r.data)

// Events
export const listEvents = (page = 1, search = '') => client.get('/events', { params: { page, search } }).then(r => r.data)
export const createEvent = (payload) => client.post('/events', payload).then(r => r.data)
export const updateEvent = (id, payload) => client.put(`/events/${id}`, payload).then(r => r.data)
export const deleteEvent = (id) => client.delete(`/events/${id}`).then(r => r.data)

// Comment Moderation
export const listCommentModeration = (page = 1) => client.get('/comment-moderation', { params: { page } }).then(r => r.data)
export const hideComment = (id) => client.post(`/comment-moderation/${id}/hide`).then(r => r.data)
export const approveComment = (id) => client.post(`/comment-moderation/${id}/approve`).then(r => r.data)
export const deleteComment = (id) => client.delete(`/comment-moderation/${id}`).then(r => r.data)

// Teacher Verification
export const listTeacherVerification = () => client.get('/teacher-verification').then(r => r.data)
export const verifyTeacher = (id) => client.post(`/teacher-verification/${id}/verify`).then(r => r.data)
export const unverifyTeacher = (id) => client.post(`/teacher-verification/${id}/unverify`).then(r => r.data)

// Backups
export const listBackups = () => client.get('/backups').then(r => r.data)
export const createBackup = () => client.post('/backups').then(r => r.data)
export const deleteBackup = (filename) => client.delete(`/backups/${filename}`).then(r => r.data)

// System Monitor
export const getSystemHealth = () => client.get('/system-monitor').then(r => r.data)

// Account Deletions
export const listAccountDeletions = (page = 1, search = '') => client.get('/account-deletions', { params: { page, search } }).then(r => r.data)
export const approveAccountDeletion = (id) => client.post(`/account-deletions/${id}/approve`).then(r => r.data)
export const rejectAccountDeletion = (id) => client.post(`/account-deletions/${id}/reject`).then(r => r.data)
