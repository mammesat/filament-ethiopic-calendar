import * as esbuild from 'esbuild'

await esbuild.build({
  entryPoints: ['test_empty_days.mjs'],
  bundle: true,
  platform: 'node',
  outfile: 'test_empty_days_bundled.cjs',
  target: ['es2020'],
  format: 'cjs',
})
