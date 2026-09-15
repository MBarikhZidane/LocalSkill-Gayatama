// One sample account buys and provides services. No authenticated session is implied.
const workspaceUser = 'barikh';
const workspaceOrders = [
  { id: 'buy-001', service: 'slides-01', buyer: 'barikh', provider: 'jamie', status: 'completed', date: '2026-09-08', brief: 'Ten slides for our campus research presentation.', message: 'The revised slides are ready. Thank you for the clear brief!' },
  { id: 'buy-002', service: 'tutoring-01', buyer: 'barikh', provider: 'aditya', status: 'in-progress', date: '2026-09-18', brief: 'A one-hour session to review difficult course concepts.', message: 'Please bring the topics you would like us to work through.' },
  { id: 'buy-003', service: 'photocopy-01', buyer: 'barikh', provider: 'rina', status: 'completed', date: '2026-09-05', brief: 'Twenty A4 black-and-white printed sides for my study notes.', message: 'Your documents were collected. Hope the study session goes well!' },
  { id: 'provide-001', service: 'design-01', buyer: 'clara', provider: 'barikh', status: 'in-progress', date: '2026-09-19', brief: 'Mood board and visual direction for a student festival.', message: 'Can we use a warm palette for the festival mood board?' },
  { id: 'provide-002', service: 'design-01', buyer: 'jamie', provider: 'barikh', status: 'completed', date: '2026-09-06', brief: 'Visual direction for a campus community launch.', message: 'The team loved the direction. Thanks for your help!' },
];
const workspaceSeedReviews = [
  { orderId: 'buy-003', rating: 5, text: 'Clear printing and an easy pickup near the library. Everything was in the right order.', date: '2026-09-06' },
];
const workspaceReceivedReviews = [
  { orderId: 'provide-002', rating: 5, text: 'Barikh gave us a clear visual direction and explained the choices well. The mood board arrived on time.', date: '2026-09-07' },
];
const workspaceDefaultProfile = {
  name: providers.barikh.name,
  role: providers.barikh.role,
  bio: providers.barikh.bio,
  levels: { Laravel: 90, PHP: 90, MySQL: 80 },
  portfolio: catalog['design-01'].portfolio.map(project => ({ ...project })),
};
// Verification is a separate, read-only sample record, never inferred from proficiency.
const workspaceVerifiedSkills = ['Laravel', 'PHP', 'MySQL'];
