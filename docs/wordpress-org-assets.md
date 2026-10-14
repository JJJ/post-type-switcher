# WordPress.org asset audit

The live WordPress.org asset directory was inspected at Subversion revision
3745095 on October 13, 2026. This audit is read-only and does not authorize an
asset publication.

| Asset | Dimensions | SHA-256 | Status |
| --- | ---: | --- | --- |
| `banner-772x250.jpg` | 772 by 250 | `0fed64e8a8ba3ca5b16f2d9d21d9b070ff889437bad734e3793d9fe9437325d7` | Valid legacy banner |
| `banner-1544x500.jpg` | 1544 by 500 | `fc1b5d6170c51d055888660a28b71343abdfec279f53489693dc03a1631a11c9` | Valid legacy banner |
| `icon-128x128.jpg` | 128 by 128 | `90eef1801585f28f4f7be93e91612be7e16d70cd0834da2fc6f039e77ced75db` | Valid legacy icon |
| `icon-256x256.jpg` | 256 by 256 | `a9be35a9f0df41d908255f91250c0774e13e4145399cc27717b9038ac408dd0f` | Valid legacy icon |
| `icon-512x512.jpg` | 512 by 512 | `629d44c6ab8fa8dd4e11b7604d06147938794c6c27f593bb2b969a4707c22d2d` | Valid legacy icon |
| `screenshot-1.png` | 880 by 320 | `095f1b40bd6ac248744a96f645a809633b8b5ec87ab54ac9a93485cc3ff725a2` | Outdated administration UI |
| `screenshot-2.png` | 880 by 430 | `afe6f748532de314f39a58be3b2edeafe3b561c80285cd63dff5f3b1fb97fb6b` | Outdated Quick Edit UI |
| `screenshot-3.png` | 880 by 430 | `dac7d7681db5611ed66e861e3adf55d268cdedb55eaa5aff326219e3ceb089a9` | Outdated Classic Editor UI |

The image dimensions are valid, but the screenshots depict an old WordPress
administration design and do not demonstrate the current block-editor
integration. A future asset change should prepare the complete banner and icon
family plus current screenshots for the list table, Quick Edit, Classic Editor,
and Block Editor. Review that complete migration diff before updating the
WordPress.org `assets` directory.
