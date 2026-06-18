const SkYouth = require('../models/SkYouth');

exports.profiling = async (req, res) => {
  const page    = parseInt(req.query.page || 1);
  const perPage = 15;
  const { rows: youth, total } = await SkYouth.getAll({ page, perPage });
  res.render('dashboard/sk/profiling', {
    title: 'SK Youth Profiling',
    youth, total, perPage, currentPage: page,
  });
};
