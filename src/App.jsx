import { Routes, Route } from "react-router-dom";
import { ThemeProvider } from "./context/ThemeContext.jsx";
import { AuthProvider } from "./context/AuthContext.jsx";
import { NotificationProvider } from "./context/NotificationContext.jsx";
import TopBanner from "./components/TopBanner.jsx";
import ActivityTracker from "./components/ActivityTracker.jsx";
import ProtectedRoute from "./components/ProtectedRoute.jsx";
import PublicLayout from "./layouts/PublicLayout.jsx";
import HomePage from "./pages/HomePage.jsx";
import AboutPage from "./pages/AboutPage.jsx";
import AbarimuPage from "./pages/AbarimuPage.jsx";
import TeacherDetailPage from "./pages/TeacherDetailPage.jsx";
import InyandikoPage from "./pages/InyandikoPage.jsx";
import IfaidaDetailPage from "./pages/IfaidaDetailPage.jsx";
import AmatangazoPage from "./pages/AmatangazoPage.jsx";
import IbitaboPage from "./pages/IbitaboPage.jsx";
import LoginPage from "./pages/LoginPage.jsx";
import RegisterPage from "./pages/RegisterPage.jsx";
import PendingApprovalPage from "./pages/PendingApprovalPage.jsx";
import ChatPage from "./pages/ChatPage.jsx";
import LiveClassPage from "./pages/LiveClassPage.jsx";
import NotificationCenterPage from "./pages/NotificationCenterPage.jsx";
import ApprovalCenterPage from "./pages/leader/ApprovalCenterPage.jsx";
import AnalyticsDashboardPage from "./pages/leader/AnalyticsDashboardPage.jsx";
import UserManagementPage from "./pages/leader/UserManagementPage.jsx";
import IfaidaListPage from "./pages/leader/IfaidaListPage.jsx";
import IfaidaEditorPage from "./pages/leader/IfaidaEditorPage.jsx";
import BooksListPage from "./pages/leader/BooksListPage.jsx";
import DarsListPage from "./pages/leader/DarsListPage.jsx";
import AnnouncementsListPage from "./pages/leader/AnnouncementsListPage.jsx";
import TeachersManagePage from "./pages/leader/TeachersManagePage.jsx";

export default function App() {
  return (
    <ThemeProvider>
      <AuthProvider>
      <NotificationProvider>
        <TopBanner />
        <ActivityTracker />
        <Routes>
        <Route element={<PublicLayout />}>
          <Route path="/" element={<HomePage />} />
          <Route path="/about" element={<AboutPage />} />
          <Route path="/abarimu" element={<AbarimuPage />} />
          <Route path="/abarimu/:id" element={<TeacherDetailPage />} />
          <Route path="/inyandiko" element={<InyandikoPage />} />
          <Route path="/inyandiko/:id" element={<IfaidaDetailPage />} />
          <Route path="/amatangazo" element={<AmatangazoPage />} />
          <Route path="/ibitabo" element={<IbitaboPage />} />
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />
          <Route path="/pending" element={<PendingApprovalPage />} />
          <Route
            path="/chat"
            element={
              <ProtectedRoute>
                <ChatPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/live-class"
            element={
              <ProtectedRoute>
                <LiveClassPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/live-class/:id"
            element={
              <ProtectedRoute>
                <LiveClassPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/notifications"
            element={
              <ProtectedRoute>
                <NotificationCenterPage />
              </ProtectedRoute>
            }
          />
        </Route>
        <Route
          path="/leader/abanyeshuri"
          element={
            <ProtectedRoute roles={["ADMIN", "LEADER"]} permission="student.approve">
              <ApprovalCenterPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="/leader/abakoresha"
          element={
            <ProtectedRoute roles={["ADMIN"]}>
              <UserManagementPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="/leader/analytics"
          element={
            <ProtectedRoute roles={["ADMIN", "LEADER"]} permission="analytics.view">
              <AnalyticsDashboardPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="/leader/ifaida"
          element={
            <ProtectedRoute roles={["ADMIN", "LEADER"]} anyOfPermissions={["ifaida.create", "ifaida.update"]}>
              <IfaidaListPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="/leader/ifaida/:id"
          element={
            <ProtectedRoute roles={["ADMIN", "LEADER"]} anyOfPermissions={["ifaida.create", "ifaida.update"]}>
              <IfaidaEditorPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="/leader/dars"
          element={
            <ProtectedRoute roles={["ADMIN", "LEADER"]} anyOfPermissions={["dars.create", "dars.update"]}>
              <DarsListPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="/leader/ibitabo"
          element={
            <ProtectedRoute roles={["ADMIN", "LEADER"]} anyOfPermissions={["book.create", "book.update", "book.view"]}>
              <BooksListPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="/leader/amatangazo"
          element={
            <ProtectedRoute roles={["ADMIN", "LEADER"]} anyOfPermissions={["announcement.create", "announcement.update"]}>
              <AnnouncementsListPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="/leader/abarimu"
          element={
            <ProtectedRoute roles={["ADMIN", "LEADER"]} permission="teacher.manage">
              <TeachersManagePage />
            </ProtectedRoute>
          }
        />
      </Routes>
      </NotificationProvider>
    </AuthProvider>
    </ThemeProvider>
  );
}
