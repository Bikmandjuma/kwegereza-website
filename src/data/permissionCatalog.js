// Mirrors backend src/utils/permissionCatalog.ts exactly. Kept in sync by
// hand since frontend and backend are separate deploys — if you add a
// permission on the backend, add it here too, in the same category.

export const PERMISSION_CATALOG = [
  { key: "student.view", category: "Abanyeshuri", label: "Kureba abanyeshuri" },
  { key: "student.approve", category: "Abanyeshuri", label: "Kwemeza abanyeshuri" },
  { key: "student.block", category: "Abanyeshuri", label: "Guhagarika abanyeshuri" },

  { key: "leader.view", category: "Abayobozi", label: "Kureba abayobozi" },
  { key: "leader.create", category: "Abayobozi", label: "Kongeramo umuyobozi" },
  { key: "leader.update", category: "Abayobozi", label: "Guhindura umuyobozi" },
  { key: "leader.block", category: "Abayobozi", label: "Guhagarika umuyobozi" },

  { key: "dars.view", category: "Dars", label: "Kureba Dars" },
  { key: "dars.create", category: "Dars", label: "Kwandika Dars" },
  { key: "dars.update", category: "Dars", label: "Guhindura Dars" },
  { key: "dars.delete", category: "Dars", label: "Gusiba Dars" },
  { key: "dars.publish", category: "Dars", label: "Gutangaza Dars" },

  { key: "ifaida.create", category: "Ifaida", label: "Kwandika Ifaida" },
  { key: "ifaida.update", category: "Ifaida", label: "Guhindura Ifaida" },
  { key: "ifaida.delete", category: "Ifaida", label: "Gusiba Ifaida" },
  { key: "ifaida.publish", category: "Ifaida", label: "Gutangaza Ifaida" },

  { key: "book.view", category: "Ibitabo", label: "Kureba ibitabo" },
  { key: "book.create", category: "Ibitabo", label: "Kongeramo igitabo" },
  { key: "book.update", category: "Ibitabo", label: "Guhindura igitabo" },
  { key: "book.delete", category: "Ibitabo", label: "Gusiba igitabo" },
  { key: "book.download", category: "Ibitabo", label: "Gukuraho ibitabo" },

  { key: "exam.view", category: "Ibizamini", label: "Kureba ibizamini" },
  { key: "exam.create", category: "Ibizamini", label: "Kwandika ikizamini" },
  { key: "exam.manage", category: "Ibizamini", label: "Gucunga ibizamini" },

  { key: "classroom.view", category: "Amasomo ya Live", label: "Kureba amasomo ya live" },
  { key: "classroom.create", category: "Amasomo ya Live", label: "Gushyiraho isomo rya live" },
  { key: "classroom.host", category: "Amasomo ya Live", label: "Gutangira no kuyobora isomo" },
  { key: "classroom.moderate", category: "Amasomo ya Live", label: "Gucunga abari mu ishuri" },

  { key: "analytics.view", category: "Isesengura", label: "Kureba isesengura rusange" },
  { key: "analytics.users", category: "Isesengura", label: "Isesengura ry'abakoresha" },
  { key: "analytics.activity", category: "Isesengura", label: "Isesengura ry'ibikorwa" },
  { key: "analytics.media", category: "Isesengura", label: "Isesengura rya media" },

  { key: "notification.send", category: "Ubutumwa", label: "Kohereza ubutumwa" },
  { key: "notification.manage", category: "Ubutumwa", label: "Gucunga ubutumwa" },

  { key: "announcement.create", category: "Amatangazo", label: "Kwandika itangazo" },
  { key: "announcement.update", category: "Amatangazo", label: "Guhindura itangazo" },
  { key: "announcement.delete", category: "Amatangazo", label: "Gusiba itangazo" },
  { key: "announcement.publish", category: "Amatangazo", label: "Gutangaza itangazo" },

  { key: "teacher.manage", category: "Abarimu", label: "Gucunga abarimu" },
];

export function groupedCatalog() {
  const groups = {};
  for (const p of PERMISSION_CATALOG) {
    if (!groups[p.category]) groups[p.category] = [];
    groups[p.category].push(p);
  }
  return groups;
}
