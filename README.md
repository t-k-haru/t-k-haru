# Portfolio
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portfolio</title>
    <style>
        /* サイト全体のデザイン設定 */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fafafa;
        }
        header {
            text-align: center;
            padding: 50px 0;
            border-bottom: 2px solid #eaeaea;
            margin-bottom: 40px;
        }
        h1 {
            margin: 0;
            font-size: 2.5em;
            color: #111;
        }
        .subtitle {
            color: #666;
            font-size: 1.2em;
            margin-top: 10px;
        }
        section {
            margin-bottom: 50px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        h2 {
            border-bottom: 2px solid #111;
            padding-bottom: 10px;
            margin-top: 0;
        }
        .project {
            border-left: 4px solid #007BFF;
            padding-left: 15px;
            margin-bottom: 25px;
        }
        .project h3 {
            margin-top: 0;
            margin-bottom: 5px;
        }
        .skills-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .skill-category {
            width: 30%;
            min-width: 200px;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <header>
        <h1>[あなたの名前を入力]</h1>
        <p class="subtitle">情報工学専攻 / クリエイター</p>
    </header>

    <section id="about">
        <h2>About Me</h2>
        <p>
            情報工学を専攻する大学2年生です。プログラミングによる論理的な課題解決と、映像制作や3DCGを用いた視覚的な表現、そしてマーケティング視点を掛け合わせた「新しい体験」の創出に関心があります。テクノロジーとクリエイティブの架け橋となるようなソリューションを生み出すことを目指しています。
        </p>
    </section>

    <section id="skills">
        <h2>Skills</h2>
        <div class="skills-container">
            <div class="skill-category">
                <h3>💻 Engineering</h3>
                <ul>
                    <li>Python / C++</li>
                    <li>HTML / CSS / JavaScript</li>
                    <li>Git / GitHub</li>
                </ul>
            </div>
            <div class="skill-category">
                <h3>🎨 Creative</h3>
                <ul>
                    <li>3DCGモデリング</li>
                    <li>映像制作・編集</li>
                    <li>UI / UX デザイン</li>
                </ul>
            </div>
            <div class="skill-category">
                <h3>📊 Business</h3>
                <ul>
                    <li>マーケティング分析</li>
                    <li>プロジェクト進行</li>
                </ul>
            </div>
        </div>
    </section>

    <section id="projects">
        <h2>Projects</h2>
        
        <div class="project">
            <h3>インタラクティブWebアート制作</h3>
            <p>ブラウザ上で動作する3DCGとユーザーの操作を連動させた作品。情報工学の知識とクリエイティブな視点を融合させ、直感的な体験を実装しました。</p>
        </div>

        <div class="project">
            <h3>都市空間・地域再開発のビジュアライズ映像</h3>
            <p>駅前拡張などの再開発プロジェクトをテーマにしたシミュレーション映像の制作。複雑な情報を視覚的に分かりやすく伝える工夫をしています。</p>
        </div>

        <div class="project">
            <h3>スマート家電のマーケティングリサーチ</h3>
            <p>最新のIoTデバイスやガジェットの市場動向を分析し、ユーザー体験を向上させるための改善案をまとめたプロジェクトです。</p>
        </div>
    </section>

    <section id="contact">
        <h2>Contact & Links</h2>
        <p>
            <a href="https://github.com/[あなたのユーザー名]" target="_blank">GitHub</a> | 
            <a href="[LinkedInのURLなど]" target="_blank">LinkedIn</a> | 
            <a href="[YouTubeなどのURL]" target="_blank">映像作品集 (YouTube)</a>
        </p>
    </section>

</body>
</html>
