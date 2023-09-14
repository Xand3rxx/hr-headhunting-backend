import {
  BaseEntity,
  Column,
  CreateDateColumn,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';

import { Exclude } from 'class-transformer';
import { IsUUID } from 'class-validator';

/**
 * Represents the base entity and provides
 * id,uuid, createdAt and updatedAt column
 */
export abstract class AbstractEntity extends BaseEntity {
  @PrimaryGeneratedColumn()
  @Exclude()
  public id!: number;

  // @IsUUID()
  // @Column({ unique: true, default: () => 'gen_random_uuid()' })
  // public uuid: string;

  @CreateDateColumn({
    type: 'timestamp',
    default: () => 'CURRENT_TIMESTAMP(6)',
  })
  public createdAt: string;

  @UpdateDateColumn({
    type: 'timestamp',
    default: () => 'CURRENT_TIMESTAMP(6)',
    onUpdate: 'CURRENT_TIMESTAMP(6)',
  })
  public updatedAt: string;
}
