import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider } from './contexts/AuthContext'
import { ToastProvider } from './contexts/ToastContext'
import { ThemeProvider } from './contexts/ThemeContext'
import { RequireAuth, RequirePermission } from './routes/guards'
import AdminLayout from './layouts/AdminLayout'
import Login from './pages/auth/Login'
import Dashboard from './pages/Dashboard'
import Forbidden from './pages/Forbidden'
import CoursesList from './pages/courses/CoursesList'
import CourseForm from './pages/courses/CourseForm'
import DarsatList from './pages/darsat/DarsatList'
import DarsatForm from './pages/darsat/DarsatForm'
import StudentsList from './pages/students/StudentsList'
import StudentDetail from './pages/students/StudentDetail'
import BooksList from './pages/books/BooksList'
import BookForm from './pages/books/BookForm'
import AnnouncementsList from './pages/announcements/AnnouncementsList'
import AnnouncementForm from './pages/announcements/AnnouncementForm'
import QuizzesList from './pages/quizzes/QuizzesList'
import QuizForm from './pages/quizzes/QuizForm'
import QuizBuilder from './pages/quizzes/QuizBuilder'
import ChatInbox from './pages/chat/ChatInbox'
import LeadersGroupChat from './pages/groupchat/LeadersGroupChat'
import AnalyticsOverview from './pages/analytics/AnalyticsOverview'
import LiveClassesList from './pages/liveclass/LiveClassesList'
import LiveClassroom from './pages/liveclass/LiveClassroom'
import ProfileSettings from './pages/profile/ProfileSettings'
import StaffList from './pages/staff/StaffList'
import StaffForm from './pages/staff/StaffForm'
import RolesList from './pages/rbac/RolesList'
import AuditLogsList from './pages/auditlogs/AuditLogsList'
import CertificatesList from './pages/admin-tools/CertificatesList'
import BadgesList from './pages/admin-tools/BadgesList'
import FeatureFlagsList from './pages/admin-tools/FeatureFlagsList'
import EventsList from './pages/admin-tools/EventsList'
import CommentModerationList from './pages/admin-tools/CommentModerationList'
import TeacherVerificationList from './pages/admin-tools/TeacherVerificationList'
import BackupsList from './pages/admin-tools/BackupsList'
import SystemMonitor from './pages/admin-tools/SystemMonitor'
import AccountDeletionsList from './pages/admin-tools/AccountDeletionsList'

export default function App() {
  return (
    <ThemeProvider>
    <AuthProvider>
      <ToastProvider>
        <BrowserRouter basename="/admin">
          <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/403" element={<Forbidden />} />

            <Route element={<RequireAuth><AdminLayout /></RequireAuth>}>
              <Route path="/dashboard" element={<Dashboard />} />

              <Route
                path="/courses"
                element={<RequirePermission permission="courses.view"><CoursesList /></RequirePermission>}
              />
              <Route
                path="/courses/new"
                element={<RequirePermission permission="courses.create"><CourseForm /></RequirePermission>}
              />
              <Route
                path="/courses/:id/edit"
                element={<RequirePermission permission="courses.update"><CourseForm /></RequirePermission>}
              />

              <Route
                path="/darsat"
                element={<RequirePermission permission="darsat.view"><DarsatList /></RequirePermission>}
              />
              <Route
                path="/darsat/new"
                element={<RequirePermission permission="darsat.create"><DarsatForm /></RequirePermission>}
              />
              <Route
                path="/darsat/:id/edit"
                element={<RequirePermission permission="darsat.update"><DarsatForm /></RequirePermission>}
              />

              <Route
                path="/students"
                element={<RequirePermission permission="students.view"><StudentsList /></RequirePermission>}
              />
              <Route
                path="/students/:id"
                element={<RequirePermission permission="students.view"><StudentDetail /></RequirePermission>}
              />

              <Route
                path="/books"
                element={<RequirePermission permission="books.view"><BooksList /></RequirePermission>}
              />
              <Route
                path="/books/new"
                element={<RequirePermission permission="books.create"><BookForm /></RequirePermission>}
              />
              <Route
                path="/books/:id/edit"
                element={<RequirePermission permission="books.update"><BookForm /></RequirePermission>}
              />

              <Route
                path="/amatangazo"
                element={<RequirePermission permission="amatangazo.view"><AnnouncementsList /></RequirePermission>}
              />
              <Route
                path="/amatangazo/new"
                element={<RequirePermission permission="amatangazo.create"><AnnouncementForm /></RequirePermission>}
              />
              <Route
                path="/amatangazo/:id/edit"
                element={<RequirePermission permission="amatangazo.update"><AnnouncementForm /></RequirePermission>}
              />

              <Route
                path="/quizzes"
                element={<RequirePermission permission="quizzes.view"><QuizzesList /></RequirePermission>}
              />
              <Route
                path="/quizzes/new"
                element={<RequirePermission permission="quizzes.create"><QuizForm /></RequirePermission>}
              />
              <Route
                path="/quizzes/:id/edit"
                element={<RequirePermission permission="quizzes.update"><QuizForm /></RequirePermission>}
              />
              <Route
                path="/quizzes/:id/builder"
                element={<RequirePermission permission="quizzes.view"><QuizBuilder /></RequirePermission>}
              />

              <Route
                path="/chat"
                element={<RequirePermission permission="chat.view"><ChatInbox /></RequirePermission>}
              />

              <Route
                path="/group-chat/leaders"
                element={<RequirePermission permission="group_chat.leaders"><LeadersGroupChat /></RequirePermission>}
              />

              <Route
                path="/analytics"
                element={<RequirePermission permission="analytics.view"><AnalyticsOverview /></RequirePermission>}
              />

              <Route
                path="/live-classes"
                element={<RequirePermission permission="live_class.manage"><LiveClassesList /></RequirePermission>}
              />
              <Route
                path="/live-classes/:id"
                element={<RequirePermission permission="live_class.manage"><LiveClassroom /></RequirePermission>}
              />

              <Route path="/profile" element={<ProfileSettings />} />

              <Route
                path="/staff"
                element={<RequirePermission permission="users.view"><StaffList /></RequirePermission>}
              />
              <Route
                path="/staff/new"
                element={<RequirePermission permission="users.create"><StaffForm /></RequirePermission>}
              />
              <Route
                path="/staff/:id/edit"
                element={<RequirePermission permission="users.update"><StaffForm /></RequirePermission>}
              />

              <Route
                path="/roles"
                element={<RequirePermission permission="roles.view"><RolesList /></RequirePermission>}
              />

              <Route
                path="/audit-logs"
                element={<RequirePermission permission="audit_logs.view"><AuditLogsList /></RequirePermission>}
              />

              <Route
                path="/certificates"
                element={<RequirePermission permission="certificates.view"><CertificatesList /></RequirePermission>}
              />
              <Route
                path="/badges"
                element={<RequirePermission permission="gamification.view"><BadgesList /></RequirePermission>}
              />
              <Route
                path="/feature-flags"
                element={<RequirePermission permission="feature_flags.manage"><FeatureFlagsList /></RequirePermission>}
              />
              <Route
                path="/events"
                element={<RequirePermission permission="events.view"><EventsList /></RequirePermission>}
              />
              <Route
                path="/comment-moderation"
                element={<RequirePermission permission="comments.moderate"><CommentModerationList /></RequirePermission>}
              />
              <Route
                path="/teacher-verification"
                element={<RequirePermission permission="teacher_verification.manage"><TeacherVerificationList /></RequirePermission>}
              />
              <Route
                path="/backups"
                element={<RequirePermission permission="backups.view"><BackupsList /></RequirePermission>}
              />
              <Route
                path="/system-monitor"
                element={<RequirePermission permission="system_monitoring.view"><SystemMonitor /></RequirePermission>}
              />
              <Route
                path="/account-deletions"
                element={<RequirePermission permission="account_deletion.manage"><AccountDeletionsList /></RequirePermission>}
              />
            </Route>

            <Route path="/" element={<Navigate to="/dashboard" replace />} />
            <Route path="*" element={<Navigate to="/dashboard" replace />} />
          </Routes>
        </BrowserRouter>
      </ToastProvider>
    </AuthProvider>
    </ThemeProvider>
  )
}
