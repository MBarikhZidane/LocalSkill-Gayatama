// Sample content for the standalone frontend. Replace with server data when connected.
const providers = {
  barikh: {
    name: 'Barikh K.', initials: 'BK', role: 'Web Developer', rating: '4.9', jobs: 24,
    location: 'Design Faculty · 0.8 km away',
    bio: 'I build clear, approachable websites and help campus teams turn their ideas into a visual direction. From a first mood board to a finished interface, I care about the details that make a project easy to use.',
    skills: ['Laravel', 'PHP', 'MySQL', 'Design', 'Visual direction', 'Mood boards'],
  },
  aditya: {
    name: 'Aditya R.', initials: 'AR', role: 'Peer Tutor', rating: '5.0', jobs: 24,
    location: 'Library zone · 0.4 km away',
    bio: 'I make tricky course concepts easier to understand with practical examples, guided exercises, and a study plan you can keep using after our session.',
    skills: ['Peer tutoring', 'Study planning', 'Concept explanation'],
  },
  jamie: {
    name: 'Jamie S.', initials: 'JS', role: 'Presentation Designer', rating: '4.9', jobs: 8,
    location: 'Library zone · 0.6 km away',
    bio: 'I turn research and project ideas into clear presentation slides, with readable charts, consistent layouts, and a story your audience can follow.',
    skills: ['Presentation slides design', 'Typography', 'Data visualization'],
  },
  nadia: {
    name: 'Nadia P.', initials: 'NP', role: 'Campus Photographer', rating: '4.9', jobs: 12,
    location: 'Arts building · 1.0 km away',
    bio: 'I photograph graduation milestones and campus events, helping you feel comfortable in front of the camera and delivering a carefully edited selection.',
    skills: ['Graduation and event photography', 'Portraits', 'Photo editing'],
  },
  clara: {
    name: 'Clara M.', initials: 'CM', role: 'Graphic Designer', rating: '4.8', jobs: 32,
    location: 'Design studio · 1.2 km away',
    bio: 'I create posters and social graphics for student communities. My focus is strong hierarchy, readable messaging, and a consistent look across formats.',
    skills: ['Graphic design', 'Poster design', 'Layout'],
  },
  rina: {
    name: 'Rina S.', initials: 'RS', role: 'Print & Photocopy Provider', rating: '4.9', jobs: 16,
    location: 'Library zone · 0.3 km away',
    bio: 'I help students prepare clean, organized printed materials. We agree on page counts, print settings, and a convenient campus pickup before I start.',
    skills: ['Photocopy services', 'Document preparation', 'Print layout'],
  },
};

const catalog = {
  'design-01': {
    title: 'Design consultation', provider: 'barikh', price: '150.000', jobs: 18,
    description: 'A one-hour design consultation with a mood board and visual direction for your campus project.',
    estimate: '1-hour consultation; mood board within 2 working days.',
    skills: ['Design', 'Visual direction', 'Mood boards'],
    portfolio: [
      { title: 'Campus community website', type: 'Website concept', description: 'A clear homepage concept for a student community, with accessible navigation and a reusable visual system.', icon: 'panels-top-left', tags: ['Laravel', 'PHP', 'MySQL'] },
      { title: 'Student festival direction', type: 'Mood board', description: 'A coordinated palette, typography selection, and layout direction for a campus festival.', icon: 'palette', tags: ['Design', 'Visual direction'] },
    ],
    reviews: [
      { name: 'Alya', rating: 5, date: '2026-08-21', text: 'The mood board made it much easier for our team to agree on a direction. Clear explanations throughout the session.' },
      { name: 'Dimas', rating: 5, date: '2026-08-12', text: 'Helpful feedback on our layout and a practical set of next steps. The notes arrived on time.' },
    ],
  },
  'tutoring-01': {
    title: 'Peer tutoring', provider: 'aditya', price: '75.000', jobs: 24,
    description: 'A one-hour peer study session to review course concepts and prepare for exams.',
    estimate: '1-hour session; recap shared within 1 working day.',
    skills: ['Peer tutoring', 'Study planning', 'Concept explanation'],
    portfolio: [
      { title: 'Exam preparation plan', type: 'Study guide', description: 'A weekly revision schedule with topic priorities and short practice exercises.', icon: 'book-open', tags: ['Study planning'] },
      { title: 'Concept practice sheets', type: 'Learning material', description: 'Worked examples and guided questions that break a difficult topic into manageable steps.', icon: 'graduation-cap', tags: ['Peer tutoring'] },
    ],
    reviews: [
      { name: 'Nisa', rating: 5, date: '2026-08-23', text: 'Patient explanations and useful examples. I left the session knowing what to practise next.' },
      { name: 'Rafi', rating: 5, date: '2026-08-15', text: 'The revision plan helped me organize my study time before the exam.' },
    ],
  },
  'slides-01': {
    title: 'Presentation slides design', provider: 'jamie', price: '50.000', jobs: 8,
    description: 'A ten-slide presentation with clear layouts, consistent typography, and one revision.',
    estimate: '3 working days after receiving your content; includes one revision.',
    skills: ['Presentation slides design', 'Typography', 'Data visualization'],
    portfolio: [
      { title: 'Research presentation', type: '10-slide deck', description: 'A structured research story with readable charts and a consistent visual hierarchy.', icon: 'presentation', tags: ['Data visualization'] },
      { title: 'Student project pitch', type: 'Pitch deck', description: 'Concise slides that introduce a campus project, its audience, and the proposed solution.', icon: 'chart-no-axes-combined', tags: ['Typography', 'Layout'] },
    ],
    reviews: [
      { name: 'Sari', rating: 5, date: '2026-08-24', text: 'Our research charts are much easier to read now. The slides feel consistent from start to finish.' },
      { name: 'Fajar', rating: 5, date: '2026-08-10', text: 'Jamie handled our revision quickly and kept the message clear.' },
    ],
  },
  'photography-01': {
    title: 'Graduation and event photography', provider: 'nadia', price: '200.000', jobs: 12,
    description: 'A one-hour graduation or campus event shoot with 20 edited photos.',
    estimate: '1-hour shoot; 20 edited photos delivered within 5 working days.',
    skills: ['Graduation and event photography', 'Portraits', 'Photo editing'],
    portfolio: [
      { title: 'Graduation portraits', type: 'Portrait series', description: 'Individual and small-group graduation portraits around the arts building.', icon: 'camera', tags: ['Portraits'] },
      { title: 'Campus creative evening', type: 'Event coverage', description: 'A curated set of candid moments, performance highlights, and group photographs.', icon: 'images', tags: ['Event photography'] },
    ],
    reviews: [
      { name: 'Intan', rating: 5, date: '2026-08-22', text: 'Nadia helped us feel relaxed during our graduation photos. The edited selection was lovely.' },
      { name: 'Bima', rating: 5, date: '2026-08-09', text: 'Great coverage of our student event and delivery within the agreed timeline.' },
    ],
  },
  'graphic-01': {
    title: 'Graphic design', provider: 'clara', price: '100.000', jobs: 32,
    description: 'A campus event poster and matching social media graphic with one revision.',
    estimate: '3 working days after the brief is confirmed; includes one revision.',
    skills: ['Graphic design', 'Poster design', 'Layout'],
    portfolio: [
      { title: 'Campus music night', type: 'Event poster', description: 'A bold poster with a clear event title, lineup, and ticket information.', icon: 'music', tags: ['Poster design'] },
      { title: 'Community recruitment', type: 'Social media graphics', description: 'Matching graphics that explain how students can join a campus community.', icon: 'pen-tool', tags: ['Graphic design', 'Layout'] },
    ],
    reviews: [
      { name: 'Putri', rating: 5, date: '2026-08-20', text: 'The poster was clear and eye-catching. Our social post matched it perfectly.' },
      { name: 'Reza', rating: 4, date: '2026-08-08', text: 'Good design and helpful communication. One revision sorted out the spacing we wanted.' },
    ],
  },
  'photocopy-01': {
    title: 'Photocopy services', provider: 'rina', price: '10.000', jobs: 16,
    description: 'Print or photocopy essays, journals, and thesis pages: 20 A4 black-and-white printed sides per package.',
    estimate: '1 working day after documents and print settings are confirmed.',
    skills: ['Photocopy services', 'Document preparation', 'Print layout'],
    portfolio: [
      { title: 'Study handout set', type: 'A4 printing', description: 'An organized set of legible black-and-white course handouts, ready for campus pickup.', icon: 'printer', tags: ['Photocopy services'] },
      { title: 'Thesis review copy', type: 'Document preparation', description: 'A clean review copy with checked page order, margins, and agreed print settings.', icon: 'files', tags: ['Document preparation'] },
    ],
    reviews: [
      { name: 'Lia', rating: 5, date: '2026-08-25', text: 'The pages were clear and in the correct order. Pickup near the library was easy.' },
      { name: 'Ardi', rating: 5, date: '2026-08-14', text: 'Rina checked the print settings with me first and had everything ready as agreed.' },
    ],
  },
};
