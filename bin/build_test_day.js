import * as esbuild from 'esbuild'

await esbuild.build({
  entryPoints: ['test_day_of_week.mjs'],
  bundle: true,
  platform: 'node',
  outfile: 'test_day_of_week_bundled.cjs',
  target: ['es2020'],
  format: 'cjs',
})
