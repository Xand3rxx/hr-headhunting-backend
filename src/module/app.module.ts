import { Module } from '@nestjs/common';
import * as Joi from 'joi';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { AppController } from '../controller/app.controller';
import { AppService } from '../service/app.service';
import { User } from '../entity/user.entity';

@Module({
  imports: [
    ConfigModule.forRoot({
      envFilePath: [
        '.local.env',
        '.development.env',
        '.staging.env',
        '.beta.env',
        '.production.env',
      ],
      cache: true,
      isGlobal: true,
      validationSchema: Joi.object({
        NODE_ENV: Joi.string()
          .valid('local', 'development', 'staging', 'beta', 'production')
          .default('development'),
        DATABASE_HOST: Joi.string().required(),
        DATABASE_PORT: Joi.number().required(),
        DATABASE_NAME: Joi.string().required(),
        DATABASE_USERNAME: Joi.string().required(),
        // DATABASE_PASSWORD: Joi.string().required(),
        DATABASE_SYNC: Joi.boolean().when('NODE_ENV', {
          is: Joi.string().equal('development'),
          then: Joi.boolean().default(true),
          otherwise: Joi.boolean().default(false),
        }),
      }),
      validationOptions: {
        allowUnknown: true,
        abortEarly: true,
      },
    }),
    TypeOrmModule.forRootAsync({
      imports: [ConfigModule],
      useFactory: (configService: ConfigService) => ({
        type: 'mysql',
        host: configService.get('DATABASE_HOST'),
        port: +configService.get('DATABASE_PORT'),
        username: configService.get('DATABASE_USERNAME'),
        password: configService.get('DATABASE_PASSWORD'),
        database: configService.get('DATABASE_NAME'),
        // entities: ['dist/**/*.entity.{ts,js}'],
        entities: [User],
        synchronize: true,
      }),
      inject: [ConfigService],
    }),
    TypeOrmModule.forFeature([User]),
  ],
  controllers: [AppController],
  providers: [AppService],
})
export class AppModule {}
